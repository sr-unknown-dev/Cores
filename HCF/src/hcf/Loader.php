<?php

declare(strict_types=1);

namespace hcf;

/**
*  ____ _               _   _          ____               
* / ___| |__   ___  ___| |_| |_   _   / ___|___  _ __ ___ 
*| |  _| '_ \ / _ \/ __| __| | | | | | |   / _ \| '__/ _ \
*| |_| | | | | (_) \__ \ |_| | |_| | | |__| (_) | | |  __/
* \____|_| |_|\___/|___/\__|_|\__, |  \____\___/|_|  \___|
*                             |___/                       
 */

use hcf\handler\bounty\BountyManager;
use hcf\handler\prefix\command\PrefixCommand;
use hcf\handler\prefix\command\PrefixsCommand;
use hcf\handler\prefix\manager\PrefixManager;
use hcf\Tasks\AutoClickTask;
use CortexPE\Commando\PacketHooker;
use hcf\abilities\AbilitiesManager;
use hcf\addons\AddonsManager;
use hcf\entity\EntityManager;
use hcf\claim\ClaimManager;
use hcf\command\CommandManager;
use hcf\command\events\EventsCommand;
use hcf\command\faction\FactionCommand;
use hcf\command\fix\FixCommand;
use hcf\command\msg\MsgCommand;
use hcf\command\msg\ReplyCommand;
use hcf\command\pay\PayCommand;
use hcf\command\pvp\PvPCommand;
use hcf\handler\kit\command\KitCommand;
use hcf\handler\kit\command\subcommand\GkitCommand;
use hcf\module\enchantment\EnchantmentManager;
use hcf\entity\CustomItemEntity;
use hcf\player\disconnected\LogoutMob;
use hcf\potion\Pots;
use hcf\timer\command\SotwCommand;
use hcf\timer\TimerManager;
use hcf\faction\FactionManager;
use hcf\koth\KothManager;
use hcf\player\disconnected\DisconnectedManager;
use hcf\session\SessionManager;
use hcf\provider\Provider;
use muqsit\invmenu\InvMenuHandler;
use hcf\entity\TextEntity;
use hcf\handler\crate\command\CrateCommand;
use hcf\handler\HandlerManager;
use hcf\item\ItemManager;
use hcf\module\ModuleManager;
use hcf\timer\types\TimerCustom;
use pocketmine\plugin\PluginBase;
use pocketmine\plugin\PluginDescription;
use pocketmine\plugin\PluginLoader;
use pocketmine\plugin\ResourceProvider;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use hcf\module\staffmode\commands\BanCommand;
use hcf\module\staffmode\commands\MuteCommand;
use hcf\module\staffmode\commands\StaffModeCommand;
use hcf\module\staffmode\commands\UnBanCommand;
use hcf\module\staffmode\commands\UnMuteCommand;
use hcf\module\staffmode\StaffListener;
use hcf\player\Player;
use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\network\mcpe\protocol\types\inventory\UseItemOnEntityTransactionData;
use hcf\HCFListener;
use hcf\module\anticheat\AntiCheatManager;
use hcf\module\anticheat\checks\AutoClick;
use hcf\module\anticheat\checks\DoubleClick;
use hcf\module\anticheat\checks\Fly;
use hcf\module\anticheat\checks\Reach;
use hcf\module\anticheat\commands\AntiCheatCommand;
use hcf\module\clearlag\ClearLag;
use hcf\module\clearlag\ClearLagCommand;
use hcf\module\ranksystem\RankManager;
use hcf\module\ranksystem\RanksTask as RanksystemRanksTask;
use hcf\module\rollback\commands\RollbackCommand;
use hcf\module\rollback\RollbackListener;
use hcf\module\rollback\RollbackManager;
use hcf\module\staffmode\commands\StaffChatCommand;
use hcf\module\staffmode\MuteAndBansTask;
use hcf\module\staffmode\StaffModeManager;
use hcf\utils\cooldowns\cdCmd;

/**
 * Class Loader
 * @package hcf
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
    /** @var Provider */
    public Provider $provider;
    /** @var EntityManager */
    public EntityManager $entityManager;
    /** @var ClaimManager */
    public ClaimManager $claimManager;
    /** @var CommandManager */
    public CommandManager $commandManager;
    /** @var EnchantmentManager */
    public EnchantmentManager $enchantmentManager;
    /** @var TimerManager */
    public TimerManager $TimerManager;
    /** @var FactionManager */
    public FactionManager $factionManager;
    /** @var KothManager */
    public KothManager $kothManager;
    /** @var DisconnectedManager */
    public DisconnectedManager $disconnectedManager;
    /** @var SessionManager */
    public SessionManager $sessionManager;
    /** @var ItemManager */
    public ItemManager $itemManager;
  /** @var AbilitiesManager */
    public AbilitiesManager $abilitiesManager;
    /** @var ModuleManager */
    public ModuleManager $moduleManager;
    /** @var HandlerManager */
    public HandlerManager $handlerManager;
    /** @var AutoClick */
    public AutoClick $autoClick;

    /** @var Reach */
    public Reach $reach;

    /** @var StaffModeManager */
    public StaffModeManager $StaffModeManager;

    /** @var AntiCheatManager */
    public AntiCheatManager $anticheatManager;

    /** @var BountyManager  */
    public BountyManager $bountyManager;

    /** @var PrefixManager */
    public PrefixManager $prefixManager;

    /** @var array */
    public static array $enderPearl = [];

    /** @var array */
    public static array $bard_allow = [];

    /** @var array */
    public array $daily1 = [];
    
    /** @var array */
    public array $daily2 = [];
    
    /** @var array */
    public array $daily3 = [];
    
    /** @var array */
    public array $daily4 = [];
    
    /** @var array */
    public array $daily5 = [];

    /** @var dms */
    public $dms = [];

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
        
        $this->provider = new Provider;
        $this->entityManager = new EntityManager;
        $this->claimManager = new ClaimManager;
        $this->commandManager = new CommandManager;
        $this->enchantmentManager = new EnchantmentManager;
        $this->TimerManager = new TimerManager;
        $this->factionManager = new FactionManager;
        $this->kothManager = new KothManager;
        $this->disconnectedManager = new DisconnectedManager;
        $this->sessionManager = new SessionManager;
        $this->itemManager = new ItemManager;
        $this->abilitiesManager = new AbilitiesManager;
        $this->handlerManager = new HandlerManager;
        $this->moduleManager = new ModuleManager;
        $this->rankManager = new RankManager();
        $this->anticheatManager = new AntiCheatManager($this);
        $this->StaffModeManager = new StaffModeManager();
        $this->bountyManager = new BountyManager();
        $this->autoClick = new AutoClick();
        $this->reach = new Reach($this, $this->getServer());
        $this->prefixManager = new PrefixManager();
        #Register addons
        AddonsManager::init();
        
        # Register listener
        $this->getServer()->getPluginManager()->registerEvents(new HCFListener(), $this);
        $this->getServer()->getPluginManager()->registerEvents(new StaffListener(), $this);
        $this->getServer()->getPluginManager()->registerEvents(new Pots(), $this);
        $this->getServer()->getPluginManager()->registerEvents(new Fly(), $this);
        $this->getServer()->getPluginManager()->registerEvents(new DoubleClick(), $this);
        $commands = [
            ["pvp", PvPCommand::class, "Comando para quitar el pvp timer"],
            ["f", FactionCommand::class, "Comando para crear una faction"],
            ["fix", FixCommand::class, "Comando para reparar items"],
            ["pay", PayCommand::class, "Comando para darle dinero a un jugador"],
            ["events", EventsCommand::class, "Comando para los eventos"],
            ["crate", CrateCommand::class, "Comando para las crates"],
            ["msg", MsgCommand::class, "Para mandar un mensaje privado a un jugador"],
            ["r", ReplyCommand::class, "Para responder mensajes"],
            ["staff", StaffModeCommand::class, "Activa el staff mode"],
            // ["freeze", FreezeCommand::class, "Para frozear a un jugador"],
            ["sc", StaffChatCommand::class, "Activa el staff chat"],
            ["sotw", SotwCommand::class, "Comandos de Sotw"],
            ["kit", KitCommand::class, "Comandos de Kits"],
            ["gkit", GkitCommand::class, "Comando de Kits"],
            ["Clear", ClearLagCommand::class],
            ["mute", MuteCommand::class, "Comando de Mute"],
            ["unmute", UnMuteCommand::class, "Comando de UnMute"],
            ["tban", BanCommand::class, "Comando de Ban"],
            ["unban", UnBanCommand::class, "Comando de UnBan"],
            ["anticheat", AntiCheatCommand::class],
        ];

        foreach ($commands as $cmd) {
            $name = $cmd[0];
            $class = $cmd[1];
            $description = $cmd[2] ?? "";
            $this->getServer()->getCommandMap()->register($name, new $class($name, $description));
        }
        $this->getServer()->getCommandMap()->registerAll("prefixsystem", [
            new PrefixCommand(),
            new PrefixsCommand()
        ]);


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

        # Tasks
        ClearLag::getInstance()->task();
        $this->getScheduler()->scheduleRepeatingTask(new RanksystemRanksTask($this), 1200);
        $this->getScheduler()->scheduleRepeatingTask(new MuteAndBansTask($this), 1200);
        # Motd
        $this->getServer()->getNetwork()->setName(TextFormat::colorize($this->getConfig()->get('motd')));
        
        $this->getScheduler()->scheduleRepeatingTask(new ClosureTask(function (): void {
            # Backup Automatic
            $this->getProvider()->save();

        }), 300 * 20);

        $this->getScheduler()->scheduleRepeatingTask(new ClosureTask(function (): void {

            # Koth
            if (($kothName = $this->getKothManager()->getKothActive()) !== null) {
                if (($koth = $this->getKothManager()->getKoth($kothName)) !== null)
                    $koth->update();
                else
                    $this->getKothManager()->setKothActive(null);
            }
            
            #Event Final
            if ($this->getTimerManager()->getDeath()->isActive()) {
                $players = $this->getServer()->getOnlinePlayers();
                
                foreach($players as $player){
                    $player->getEffects()->add(new EffectInstance(VanillaEffects::STRENGTH(), 20 * 7, 1));
                }
            }

            # Events
            # $this->getServer()->getCommandMap()->register("z", new cdCmd());
            $this->getTimerManager()->getSotw()->update();
            $this->getTimerManager()->getEotw()->update();
            $this->getTimerManager()->getPurge()->update();
            $this->getTimerManager()->getPoints()->update();
            $this->getTimerManager()->getDeath()->update();
            foreach($this->getTimerManager()->getCustomTimers() as $name => $timer){
                if($timer instanceof TimerCustom)
                    $timer->update();
            }

            # Logouts
            foreach($this->getServer()->getWorldManager()->getDefaultWorld()->getEntities() as $entity) {
                if($entity instanceof LogoutMob) {
                    $entity->onUpdate(20);
                }
            }
                
            # Sessions
            foreach ($this->getSessionManager()->getSessions() as $session)
                $session->onUpdate();
                
            # Factions
            foreach ($this->getFactionManager()->getFactions() as $faction)
                $faction->onUpdate();
        }), 20);
    }

    public function onPacketReceive(DataPacketReceiveEvent $event) : void {
        $packet = $event->getPacket();
        $player = $event->getOrigin()->getPlayer();
        if ($player instanceof Player) {
            $this->getAutoClick()->run($packet, $player);
            
            if ($packet instanceof InventoryTransactionPacket && $packet->trData instanceof UseItemOnEntityTransactionData) {
                $target = $player->getWorld()->getEntity($packet->trData->getActorRuntimeId());
                if ($target instanceof Player) {
                    $distance = $player->getPosition()->distance($target->getPosition());
                    $this->getReach()->run($player, $distance);
                }
            }
        }
    }
    
    protected function onDisable(): void
    {
        $this->getProvider()->save();
        $this->disconnectedManager->onDisable();
        
        $world = $this->getServer()->getWorldManager()->getDefaultWorld();
        foreach ($world->getEntities() as $entity) {
            if ($entity instanceof CustomItemEntity || $entity instanceof TextEntity)
                $entity->kill();
        }

        $this->getScheduler()->scheduleRepeatingTask(new ClosureTask(function (): void {
            # Backup Automatic
            $this->getProvider()->save();

        }), 300 * 20);
    }

    /**
     * @return Loader
     */
    public static function getInstance(): Loader
    {
        return self::$instance;
    }
    
    public function getAbilitiesManager(): AbilitiesManager{
        return $this->abilitiesManager;
    }
    /**
     * @return Provider
     */
    public function getProvider(): Provider{
        return $this->provider;
    }
    
    /**
     * @return EntityManager
     */
    public function getEntityManager(): EntityManager
    {
        return $this->entityManager;
    }
    
    /**
     * @return ClaimManager
     */
    public function getClaimManager(): ClaimManager
    {
        return $this->claimManager;
    }
    
    /**
     * @return CommandManager
     */
    public function getCommandManager(): CommandManager
    {
        return $this->commandManager;
    }
    
    /**
     * @return EnchantmentManager
     */
    public function getEnchantmentManager(): EnchantmentManager
    {
        return $this->enchantmentManager;
    }
    
    /**
     * @return TimerManager
     */
    public function getTimerManager(): TimerManager
    {
        return $this->TimerManager;
    }
    
    /**
     * @return FactionManager
     */
    public function getFactionManager(): FactionManager
    {
        return $this->factionManager;
    }
    
    /**
     * @return KothManager
     */
    public function getKothManager(): KothManager
    {
        return $this->kothManager;
    }
    
    /**
     * @return DisconnectedManager
     */
    public function getDisconnectedManager(): DisconnectedManager
    {
        return $this->disconnectedManager;
    }
    
    /**
     * @return SessionManager
     */
    public function getSessionManager(): SessionManager
    {
        return $this->sessionManager;
    }

    public function getItemManager(): ItemManager
    {
        return $this->itemManager;
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

    public function getBountyManager(): BountyManager{
        return $this->bountyManager;
    }

    public function getAutoClick(): AutoClick {
        return $this->autoClick;
    }

    public function getReach(): Reach {
        return $this->reach;
    }

    /**
     * @return PrefixManager
     */
    public function getPrefixManager(): PrefixManager
    {
        return $this->prefixManager;
    }
}
