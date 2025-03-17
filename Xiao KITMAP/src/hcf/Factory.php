<?php

namespace hcf;

use hcf\handler\airdrop\AirdropManager;
use hcf\player\Player;
use hcf\StaffMode\Freeze;
use hcf\StaffMode\Vanish;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\VanillaItems;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use pocketmine\network\mcpe\protocol\types\InputMode;
use pocketmine\Server;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\handler\Listener;
use pocketmine\event\inventory\InventoryTransactionEvent;

class Factory{

    public static array $players = [];
    public $name = [];
    private $reports;
    public const DIAMOND = "§bDiamond§r";
    public const SELECTE_DIAMOND = "§cDiamond§r";

    public static function setNick(Player $player, $name){
        $player->setNameTag($name);
        $player->setDisplayName($name);
        $player->getSession()->setName($name);
    }

    public static function LFFMenu(Player $player): void
    {
        self::$players[$player->getName()]["Diamond"] = false;
        self::$players[$player->getName()]["Bard"] = false;
        self::$players[$player->getName()]["Archer"] = false;
        self::$players[$player->getName()]["Rogue"] = false;
        self::$players[$player->getName()]["Mague"] = false;
        $menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
        foreach(self::ItemsLives() as $slot => $item){
            $menu->getInventory()->setItem($slot, $item);
        }
        $menu->setListener(listener: function (InvMenuTransaction $transaction) use($menu): InvMenuTransactionResult {
            $item = $transaction->getItemClicked();

            if ($item->getCustomName() === "§bDiamond§r") {
                $menu->getInventory()->setItem(20, $item->setCustomName("§cDiamond§r"));
                self::$players[$transaction->getPlayer()->getName()]["Diamond"] = true;
                return $transaction->discard();
            }elseif ($item->getCustomName() === "§cDiamond§r"){
                $menu->getInventory()->setItem(20, $item->setCustomName("§bDiamond§r"));
                self::$players[$transaction->getPlayer()->getName()]["Diamond"] = false;
                return $transaction->discard();
            }
            if ($item->getCustomName() === "§gBard§r") {
                $menu->getInventory()->setItem(21, $item->setCustomName("§cBard§r"));
                self::$players[$transaction->getPlayer()->getName()]["Bard"] = true;
                return $transaction->discard();
            }elseif ($item->getCustomName() === "§cBard§r"){
                $menu->getInventory()->setItem(21, $item->setCustomName("§gBard§r"));
                self::$players[$transaction->getPlayer()->getName()]["Bard"] = false;
                return $transaction->discard();
            }
            if ($item->getCustomName() === "§5Archer§r") {
                $menu->getInventory()->setItem(22, $item->setCustomName("§cArcher§r"));
                self::$players[$transaction->getPlayer()->getName()]["Archer"] = true;
                return $transaction->discard();
            }elseif ($item->getCustomName() === "§cArcher"){
                $menu->getInventory()->setItem(22, $item->setCustomName("§5Archer§r"));
                self::$players[$transaction->getPlayer()->getName()]["Archer"] = false;
                return $transaction->discard();
            }
            if ($item->getCustomName() === "§7Rogue§r") {
                $menu->getInventory()->setItem(23, $item->setCustomName("§cRogue§r"));
                self::$players[$transaction->getPlayer()->getName()]["Rogue"] = true;
                return $transaction->discard();
            }elseif ($item->getCustomName() === "§cRogue§r"){
                $menu->getInventory()->setItem(23, $item->setCustomName("§7Rogue§r"));
                self::$players[$transaction->getPlayer()->getName()]["Rogue"] = false;
                return $transaction->discard();
            }
            if ($item->getCustomName() === "§6Mague§r") {
                $menu->getInventory()->setItem(24, $item->setCustomName("§cMague§r"));
                self::$players[$transaction->getPlayer()->getName()]["Mague"] = true;
                return $transaction->discard();
            }elseif ($item->getCustomName() === "§cMague§r"){
                $menu->getInventory()->setItem(24, $item->setCustomName("§6Mague§r"));
                self::$players[$transaction->getPlayer()->getName()]["Mague"] = false;
                return $transaction->discard();
            }
            if ($item->getCustomName() === "§6Send Message") {
                $transaction->getPlayer()->removeCurrentWindow();
                Loader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&8(&l&gSYSTEM LFF&r&8)\n&b".$transaction->getPlayer()->getName()." &7Is Looking For A Faction\n&7-&r".self::getClass($transaction->getPlayer())));
                self::$players[$transaction->getPlayer()->getName()]["Diamond"] = false;
                self::$players[$transaction->getPlayer()->getName()]["Bard"] = false;
                self::$players[$transaction->getPlayer()->getName()]["Archer"] = false;
                self::$players[$transaction->getPlayer()->getName()]["Rogue"] = false;
                self::$players[$transaction->getPlayer()->getName()]["Mague"] = false;
                return $transaction->discard();
            }
            return $transaction->discard();
        });
        $menu->send($player, "§gLFF Menu");
    }

    /**
     * @return array
     */
    public static function ItemsLives(): array{
        $items = [
            20 => VanillaItems::DIAMOND_HELMET()->setCustomName("§bDiamond§r"),
            21 => VanillaItems::GOLDEN_HELMET()->setCustomName("§gBard§r"),
            22 => VanillaItems::LEATHER_CAP()->setCustomName("§5Archer§r"),
            23 => VanillaItems::CHAINMAIL_HELMET()->setCustomName("§7Rogue§r"),
            24 => VanillaItems::CHAINMAIL_HELMET()->setCustomName("§6Mague§r"),
            40 => VanillaItems::PAPER()->setCustomName("§6Send Message")
        ];
        return $items;
    }

    /**
     * @param Player $player
     * @return string
     */
    public static function getClass(Player $player): string{
        $class = [];
        if (self::$players[$player->getName()]["Diamond"] === true) {
            $class[] = "§bDiamond§r";
        }
        if (self::$players[$player->getName()]["Bard"] === true) {
            $class[] = "§gBard§r";
        }
        if (self::$players[$player->getName()]["Archer"] === true) {
            $class[] = "§5Archer§r";
        }
        if (self::$players[$player->getName()]["Rogue"] === true) {
            $class[] = "§7Rogue§r";
        }
        if (self::$players[$player->getName()]["Mague"] === true) {
            $class[] = "§6Mague§r";
        }
        return implode("\n§7-§r", $class);
    }

    public static function NewConfig(Player $player) {
        $name = [];
        $input = self::getInputMode($player);
        $config = new Config(Loader::getInstance()->getDataFolder()."players.yml", Config::YAML, ["player" => $player->getName(), "input" => $input]);
        $config->set("");
    }

    public static function getInputMode(Player $player): string{
        $data = $player->getPlayerInfo()->getExtraData();
        return match ($data["CurrentInputMode"]) {
            InputMode::TOUCHSCREEN => "Touch",
            InputMode::MOUSE_KEYBOARD => "Keyboard",
            InputMode::GAME_PAD => "Controller",
            InputMode::MOTION_CONTROLLER => "Motion Controller",
            default => "Unknown"
        };
    }

    public static function invSee(Player $player, Player $target): void
    {
        $menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
        $menu->getInventory()->setContents(array_merge($target->getInventory()->getContents(), $target->getArmorInventory()->getContents()));
        $menu->setListener(InvMenu::readonly());
        $menu->send($player, TextFormat::colorize('&c' . $target->getName() . '\'s inventory'));
    }

    public static function sendMsg(Player $player, $sender, $msg)
    {
        $sender->sendMessage(TextFormat::colorize("&8(&gTo&8) &g".$player->getName().": &7".$msg));
        $player->sendMessage(TextFormat::colorize("&8(&gFrom&8) &g".$sender->getName().": &7".$msg));
    }
    
    public static function getAntiCheatFile() : Config {
        return new Config(Loader::getInstance()->getDataFolder()."kb-module.yml", Config::YAML);
    }

    public static function getAirdrop(Player $player, int $count)
    {
        $contents = [];
        $crateItems = AirdropManager::getAirdrop()->getItems();
        $ItemNames = [];
    
        foreach ($crateItems as $item) {
            $name = trim($item->getName());
            if ($name !== '') {
                $ItemNames[] = $name;
            }
        }
    
        $airdrop = VanillaBlocks::CHEST()->asItem();
        $airdrop->setCustomName("§l§3Airdrop");
        $airdrop->setCount($count);
        $lore = implode("\n", array_map([TextFormat::class, 'colorize'], $ItemNames));
        $airdrop->setLore([$lore]);
        $airdrop->getNamedTag()->setString("Airdrop_Item", "Airdrop");
        $player->getInventory()->addItem($airdrop);
    }
}
?>