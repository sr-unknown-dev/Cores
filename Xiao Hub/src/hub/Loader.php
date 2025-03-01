<?php

declare(strict_types=1);

namespace hub;

/**
*  ____ _               _   _          ____               
* / ___| |__   ___  ___| |_| |_   _   / ___|___  _ __ ___ 
*| |  _| '_ \ / _ \/ __| __| | | | | | |   / _ \| '__/ _ \
*| |_| | | | | (_) \__ \ |_| | |_| | | |__| (_) | | |  __/
* \____|_| |_|\___/|___/\__|_|\__, |  \____\___/|_|  \___|
*                             |___/                       
 */

use CortexPE\Commando\PacketHooker;
use hub\abilities\AbilitiesManager;
use hub\addons\AddonsManager;
use hub\claim\ClaimManager;
use hub\command\CommandManager;
use hub\command\events\EventsCommand;
use hub\command\faction\FactionCommand;
use hub\command\fix\FixCommand;
use hub\command\msg\MsgCommand;
use hub\command\msg\ReplyCommand;
use hub\command\pay\PayCommand;
use hub\command\pvp\PvPCommand;
use hub\entity\CustomItemEntity;
use hub\entity\EntityManager;
use hub\entity\TextEntity;
use hub\faction\FactionManager;
use hub\handler\bounty\BountyManager;
use hub\handler\crate\command\CrateCommand;
use hub\handler\HandlerManager;
use hub\handler\kit\command\KitCommand;
use hub\handler\kit\command\subcommand\GkitCommand;
use hub\handler\knockback\commands\KnockBackCommand;
use hub\handler\knockback\KnockBackListener;
use hub\handler\knockback\KnockBackManager;
use hub\item\ItemManager;
use hub\koth\KothManager;
use hub\module\anticheat\AntiCheatManager;
use hub\module\anticheat\checks\AutoClick;
use hub\module\anticheat\checks\DoubleClick;
use hub\module\anticheat\checks\Fly;
use hub\module\anticheat\checks\Reach;
use hub\module\anticheat\commands\AntiCheatCommand;
use hub\module\clearlag\ClearLag;
use hub\module\clearlag\ClearLagCommand;
use hub\module\enchantment\EnchantmentManager;
use hub\module\ModuleManager;
use hub\module\ranksystem\RankManager;
use hub\module\ranksystem\RanksTask as RanksystemRanksTask;
use hub\module\rollback\commands\RollbackCommand;
use hub\module\rollback\RollbackListener;
use hub\module\rollback\RollbackManager;
use hub\module\staffmode\commands\BanCommand;
use hub\module\staffmode\commands\MuteCommand;
use hub\module\staffmode\commands\StaffChatCommand;
use hub\module\staffmode\commands\StaffModeCommand;
use hub\module\staffmode\commands\UnBanCommand;
use hub\module\staffmode\commands\UnMuteCommand;
use hub\module\staffmode\MuteAndBansTask;
use hub\module\staffmode\StaffListener;
use hub\module\staffmode\StaffModeManager;
use hub\player\disconnected\DisconnectedManager;
use hub\player\disconnected\LogoutMob;
use hub\player\Player;
use hub\potion\Pots;
use hub\prefix\PrefixManager;
use hub\provider\Provider;
use hub\session\SessionManager;
use hub\Tasks\AutoClickTask;
use hub\timer\command\SotwCommand;
use hub\timer\TimerManager;
use hub\timer\types\TimerCustom;
use muqsit\invmenu\InvMenuHandler;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\network\mcpe\protocol\types\inventory\UseItemOnEntityTransactionData;
use pocketmine\plugin\PluginBase;
use pocketmine\plugin\PluginDescription;
use pocketmine\plugin\PluginLoader;
use pocketmine\plugin\ResourceProvider;
use pocketmine\scheduler\ClosureTask;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

/**
 * Class Loader
 * @package hub
 */
class Loader extends PluginBase implements Listener
{

    /**
     * @var RankManager
     */
    private $rankManager;

    public function __construct(PluginLoader $loader, Server $server, PluginDescription $description, string $dataFolder, string $file, ResourceProvider $resourceProvider)
    {
        parent::__construct($loader, $server, $description, $dataFolder, $file, $resourceProvider);
    }

    const ALERTS = "§8[§l§4Alerts§r§8]: ";

    public array $items;
    public array $offhand;
    public array $armor;
    public array $position;
    
    /** @var Loader */
    public static Loader $instance;
    /** @var EntityManager */
    public EntityManager $entityManager;
    /** @var PrefixManager */
    public PrefixManager $prefixManager;
    /** @var ModuleManager */
    public ModuleManager $moduleManager;
    /** @var HandlerManager */
    public HandlerManager $handlerManager;

    /** @var StaffModeManager */
    public StaffModeManager $StaffModeManager;

    protected function onLoad(): void
    {
        self::$instance = $this;
    }
    
    protected function onEnable() : void
    {
        @mkdir($this->getDataFolder() . "/Skins/");
        $this->getServer()->getPluginManager()->registerEvents($this, $this);

        $commands = [
            'transferserver',
            'extractplugin',
            'tell',
            'w',
            'ban',
            'unban',
            'unban-ip',
            'ban-ip'
        ];
        
        foreach ($commands as $commandName) {
            $command = $this->getServer()->getCommandMap()->getCommand($commandName);
            
            if ($command !== null)
                $this->getServer()->getCommandMap()->unregister($command);
        }

        if (!InvMenuHandler::isRegistered())
	        InvMenuHandler::register($this);

        if (!PacketHooker::isRegistered())
            PacketHooker::register($this);

        $this->handlerManager = new HandlerManager;
        $this->moduleManager = new ModuleManager;
        $this->rankManager = new RankManager();
        $this->StaffModeManager = new StaffModeManager();
        
        # Register listener
        $this->getServer()->getPluginManager()->registerEvents(new HubListener(), $this);
        $this->getServer()->getPluginManager()->registerEvents(new StaffListener(), $this);
        $this->getServer()->getCommandMap()->register("staff", new StaffModeCommand("staff", "Activa el staff mode"));
        #$this->getServer()->getCommandMap()->register("freeze", new FreezeCommand("freeze", "Para frozear a un jugador"));
        $this->getServer()->getCommandMap()->register("sc", new StaffChatCommand("sc", "Activa el staff chat"));
        $this->getServer()->getCommandMap()->register("mute", new MuteCommand("mute", "Comando de Mute"));
        $this->getServer()->getCommandMap()->register("ban", new BanCommand("tban", "Comando de Ban"));
        $this->getServer()->getCommandMap()->register("mute", new UnMuteCommand("unmute", "Comando de UnMute"));
        $this->getServer()->getCommandMap()->register("ban", new UnBanCommand("unban", "Comando de UnBan"));
        $this->getServer()->getCommandMap()->register("anticheat", new AntiCheatCommand());

        #Loger
        $this->getLogger()->info("  ____ _               _   _          ____               ");
        $this->getLogger()->info(" / ___| |__   ___  ___| |_| |_   _   / ___|___  _ __ ___ ");
        $this->getLogger()->info("| |  _| '_ \ / _ \/ __| __| | | | | | |   / _ \| '__/ _ \ ");
        $this->getLogger()->info("| |_| | | | | (_) \__ \ |_| | |_| | | |__| (_) | | |  __/");
        $this->getLogger()->info(" \____|_| |_|\___/|___/\__|_|\__, |  \____\___/|_|  \___|");
        $this->getLogger()->info("                             |___/                       ");
        $this->getLogger()->info("");
        $this->getLogger()->info("  ____ _____  _    _____ _____   __  __  ___  ____  _____  ");
        $this->getLogger()->info(" / ___|_   _|/ \  |  ___|  ___| |  \/  |/ _ \|  _ \| ____| ");
        $this->getLogger()->info(" \___ \ | | / _ \ | |_  | |_    | |\/| | | | | | | |  _|   ");
        $this->getLogger()->info("  ___) || |/ ___ \|  _| |  _|   | |  | | |_| | |_| | |___  ");
        $this->getLogger()->info(" |____/ |_/_/   \_\_|   |_|     |_|  |_|\___/|____/|_____| ");
        $this->getLogger()->info("");
        $this->getLogger()->info("  _  ___   _  ___   ____ _  ______    _    ____ _  __ ");
        $this->getLogger()->info(" | |/ / \ | |/ _ \ / ___| |/ / __ )  / \  / ___| |/ / ");
        $this->getLogger()->info(" | ' /|  \| | | | | |   | ' /|  _ \ / _ \| |   | ' /  ");
        $this->getLogger()->info(" | . \| |\  | |_| | |___| . \| |_) / ___ \ |___| . \  ");
        $this->getLogger()->info(" |_|\_\_| \_|\___/ \____|_|\_\____/_/   \_\____|_|\_\ ");


        $this->getScheduler()->scheduleRepeatingTask(new RanksystemRanksTask($this), 1200);
        $this->getScheduler()->scheduleRepeatingTask(new MuteAndBansTask($this), 1200);
        # Motd
        $this->getServer()->getNetwork()->setName(TextFormat::colorize($this->getConfig()->get('motd')));
    }
    
    protected function onDisable(): void
    {

    }

    /**
     * @return Loader
     */
    public static function getInstance(): Loader
    {
        return self::$instance;
    }
    
    public function getPrefixManager(): PrefixManager
    {
        return $this->prefixManager;
    }
    
    /**
     * @return EntityManager
     */
    public function getEntityManager(): EntityManager
    {
        return $this->entityManager;
    }
    
    public function getModuleManager(): ModuleManager 
    {
        return $this->moduleManager;
    }

    public function getHandlerManager(): HandlerManager {
        return $this->handlerManager;
    }

    public function getRankManager(): RankManager {
        return $this->rankManager;
    }

    public function getStaffModeManager(): StaffModeManager {
        return $this->StaffModeManager;
    }

    public function getAntiCheatManager(): AntiCheatManager{
        return $this->anticheatManager;
    }
}
