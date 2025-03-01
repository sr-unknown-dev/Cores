<?php

namespace hub\module\staffmode;

use hub\Loader;
use hub\player\Player;
use muqsit\invmenu\InvMenu;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\protocol\types\DeviceOS;
use pocketmine\network\mcpe\protocol\types\InputMode;
use pocketmine\player\GameMode;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

class StaffModeManager {
    public array $staff = [];
    public array $vanish = [];
    public array $freeze = [];
    public array $items = [];
    public array $offhand = [];
    public array $armor = [];
    public array $staffs = [];
    public array $god = [];
    public array $staffchat = [];
    public Config $mutes;
    public Config $bans;

    public function __construct() {
        $dataFolder = Loader::getInstance()->getDataFolder();
        $this->mutes = new Config($dataFolder . "mutes.json", Config::JSON);
        $this->bans = new Config($dataFolder . "bans.json", Config::JSON);
    }

    public function addStaff(Player $p): void {
        $pName = $p->getName();
        $this->staff[$pName] = true;
        $this->saveItems($p);

        $p->getInventory()->clearAll();
        $p->getOffHandInventory()->clearAll();
        $p->getArmorInventory()->clearAll();

        $p->setGamemode(GameMode::SURVIVAL());
        $p->getHungerManager()->setFood($p->getHungerManager()->getMaxFood());
        $p->setHealth($p->getMaxHealth());
        $p->setFlying(true);
        $p->setAllowFlight(true);

        $this->setItems($p);
        $this->staffs[$pName] = ["gamemode" => $p->getGamemode(), "position" => $p->getPosition()];

        $p->setNameTag("§8[§6StaffMode§8]\n§7$pName");
        $p->setNameTagVisible(true);
        $p->setNameTagAlwaysVisible(true);
        $this->toggleVanish($p);
        $p->sendMessage("§8[§6StaffMode§8]: §aEnable");
    }

    public function removeStaff(Player $p): void {
        $pName = $p->getName();
        unset($this->staff[$pName]);

        $p->getInventory()->clearAll();
        $p->getOffHandInventory()->clearAll();
        $p->getArmorInventory()->clearAll();

        $p->setGamemode($this->staffs[$pName]["gamemode"]);
        $this->restoreItems($p);
        $p->setFlying(false);
        $p->setAllowFlight(false);
        $p->setNameTag($pName);
        $p->teleport($this->staffs[$pName]["position"] ?? $p->getPosition());
        $this->toggleVanish($p);
        $p->sendMessage("§8[§6StaffMode§8]: §4Disable");
    }

    private function setItems(Player $p): void {
        $items = [];
        
        $compass = VanillaItems::COMPASS();
        $compass->setCustomName("§gTeleport");
        $compass->setLore(["§7Click for random teleport\n§3@StaffTeam"]);
        $compass->getNamedTag()->setString("staffs", "teleport");
        $items[0] = $compass;

        $ice = VanillaBlocks::PACKED_ICE()->asItem();
        $ice->setCustomName("§bFreeze");
        $ice->setLore(["§7Click to freeze player\n§3@StaffTeam"]);
        $ice->getNamedTag()->setString("staffs", "freeze");
        $items[1] = $ice;

        $dye = VanillaItems::DYE();
        $dye->setColor(DyeColor::LIME());
        $dye->setCustomName("§aVanish");
        $dye->setLore(["§7Click to toggle vanish\n§3@StaffTeam"]);
        $dye->getNamedTag()->setString("staffs", "vanish");
        $items[4] = $dye;

        $chest = VanillaBlocks::CHEST()->asItem();
        $chest->setCustomName("§4Invsee");
        $chest->setLore(["§7Click to view inventory\n§3@StaffTeam"]);
        $chest->getNamedTag()->setString("staffs", "invsee");
        $items[7] = $chest;

        $stick = VanillaItems::STICK();
        $stick->setCustomName("§gInformation");
        $stick->setLore(["§7Click to view player info\n§3@StaffTeam"]);
        $stick->getNamedTag()->setString("staffs", "info");
        $items[8] = $stick;

        $p->getInventory()->setContents($items);
    }

    private function saveItems(Player $p): void {
        $name = $p->getName();
        $this->items[$name] = $p->getInventory()->getContents();
        $this->offhand[$name] = $p->getOffHandInventory()->getContents();
        $this->armor[$name] = $p->getArmorInventory()->getContents();
    }

    private function restoreItems(Player $p): void {
        $name = $p->getName();
        if(isset($this->items[$name])) {
            $p->getInventory()->setContents($this->items[$name]);
            unset($this->items[$name]);
        }
        if(isset($this->offhand[$name])) {
            $p->getOffHandInventory()->setContents($this->offhand[$name]);
            unset($this->offhand[$name]);
        }
        if(isset($this->armor[$name])) {
            $p->getArmorInventory()->setContents($this->armor[$name]);
            unset($this->armor[$name]);
        }
    }

    public function isStaff(Player $p): bool {
        return isset($this->staff[$p->getName()]);
    }

    public function toggleVanish(Player $p): void {
        if (isset($this->vanish[$p->getName()])) {
            unset($this->vanish[$p->getName()]);
            foreach (Server::getInstance()->getOnlinePlayers() as $player) {
                if ($player->hasPermission("staff.perms")){
                    $player->showPlayer($p);
                }
            }
            $p->sendMessage("§8[§6StaffMode§8]: §fVanish disabled");
        }else {
            $this->vanish[$p->getName()] = true;
            foreach (Server::getInstance()->getOnlinePlayers() as $player) {
                if ($player->hasPermission("staff.perms")){
                    $player->hidePlayer($p);
                }
            }
            $p->sendMessage("§8[§6StaffMode§8]: §fVanish enabled");
        }
    }

    public function isVanish(Player $p): bool {
        return isset($this->vanish[$p->getName()]);
    }

    public function getVanish(): array {
        return $this->vanish;
    }

    public function toggleGod(Player $player): void {
        if (isset($this->god[$player->getName()])) {
            unset($this->god[$player->getName()]);
            $player->setGod(false);
            $player->sendMessage(TextFormat::colorize('&l&bGod: &r&cDisable'));
          } else {
            $this->god[$player->getName()] = true;
            $player->setGod(true);
            $player->sendMessage(TextFormat::colorize('&l&bGod: &r&aEnable'));
          }
    }

    public function isGod(Player $player): bool {
        return isset($this->god[$player->getName()]);
    }

    public function toggleFreeze(Player $p): void {
        if(isset($this->freeze[$p->getName()])){
            unset($this->freeze[$p->getName()]);
            $p->sendMessage("§8[§6StaffMode§8]: §fHaz sido unfrozeado");
        }else {
            $this->freeze[$p->getName()] = true;
            $p->sendMessage("§8[§6StaffMode§8]: §fHaz sido frozeado");
        }
    }

    public function isFreeze(Player $p): bool {
        return isset($this->freeze[$p->getName()]);
    }

    public function toggleStaffChat(Player $s){
    	if(isset($this->staffchat[$s->getName()])){
            unset($this->staffchat[$s->getName()]);
            $s->sendMessage("§8[§4StaffChat§8]: §fHaz salido del staff chat");
        }else {
            $this->staffchat[$s->getName()] = true;
            $s->sendMessage("§8[§4StaffChat§8]: §fHaz entrado al staff chat");
        }
    }

    public function isStaffChat(Player $player): bool {
        return isset($this->staffchat[$player->getName()]);
    }

    public function hasPermission(Player $p): bool {
        return $p->hasPermission("staff.perms");
    }

    public function addMute(Player $s, Player $t, string $reason, string $time): void {
        $tName = $t->getName();
        $p = $s ?? "AntiCheat";
        if(!($timeSeconds = $this->parseTime($time)) || $this->mutes->exists($tName)) {
            $p->sendMessage($timeSeconds === null ? "§4Invalid time format" : "§4Player §6$tName §4is already muted");
            return;
        }
        $this->mutes->set($tName, ['reason' => $reason, 'time' => time() + $timeSeconds]);
        $this->mutes->save();
        $p->sendMessage("§aMuted §6$tName §afor: §6$reason §afor: §6$time");
        $t->sendMessage("§4You have been muted\nReason: §6$reason\n§4Expires in: §6" . $this->formatTime($timeSeconds));
    }

    public function removeMute(Player $p, Player $t): void {
        $tName = $t->getName();
        if(!$this->mutes->exists($tName)) {
            $p->sendMessage("§4Player §6$tName §4is not muted");
            return;
        }
        $this->mutes->remove($tName);
        $this->mutes->save();
        $p->sendMessage("§aUnmuted §6$tName");
    }

    public function isMute(Player $p): bool {
        return $this->mutes->exists($p->getName());
    }

    public function addBanAntiCheat(Player $target, string $reason, string $time): void {
        $tName = $target->getName();
        if(!($timeSeconds = $this->parseTime($time)) || $this->bans->exists($tName)) {
            return;
        }

        $this->bans->set($tName, ['reason' => $reason, 'time' => time() + $timeSeconds]);
        $this->bans->save();

        foreach(Server::getInstance()->getOnlinePlayers() as $op) {
            if($op->hasPermission("staff.perms")) {
                $op->sendMessage("§8[§cAntiCheat§8] §aBanned §6$tName §afor: §6$reason §afor: §6$time");
            }
        }

        $target->kick("§4You have been banned\nReason: §6$reason\n§4Expires in: §6" .
            $this->formatTime($timeSeconds) . " §7Appeal at: §6" .
            Loader::getInstance()->getConfig()->get("discord-link"));
    }

    public function addBan(Player $s, Player $t, string $rason, string $time)
    {
        $tName = $t->getName();
        $time = $this->parseTime($time);
        if ($time === null){$s->sendMessage("§4Formato d tiempo invalido");}
        if ($this->bans->exists($tName)){$s->sendMessage("§4El player: §6".$tName." §4ya a sido baneado");}

        $this->bans->set($tName, [
            'reazon' => $rason,
            'time' => time() + $time
        ]);
        $this->bans->save();
        $rTime = time() + $time - time();
        $s->sendMessage("§aHas baneado a: §6".$tName." §apor: §6".$rason." §adurante: §6".$time);
        $t->kick("§4Has sido baneado\nRazon: §6".$rason."\n§4Expira en: §6".$this->formatTime($rTime)." §7Si deseas apelar el ban: §6".Loader::getInstance()->getConfig()->get("discord-link"));
    }

    public function removeBan(Player $s, Player $t): void {
        $tName = $t->getName();
        if(!$this->bans->exists($tName)) {
            $s->sendMessage("§4El player: §6".$tName." §4no esta baneado");
            return;
        }
        $this->bans->remove($tName);
        $this->bans->save();
        $s->sendMessage("§aHas desbaneado a: §6".$tName);
    }

    public function isBan(Player $p): bool {
        return $this->bans->exists($p->getName());
    }

    public function parseTime(string $duration): ?int {
        if(!preg_match_all('/(\d+)([mhwd])/', $duration, $matches, PREG_SET_ORDER)) return null;
        $units = ['m' => 60, 'h' => 3600, 'd' => 86400, 'w' => 604800];
        return array_reduce($matches, function($carry, $match) use ($units) {
            return isset($units[$match[2]]) ? $carry + ((int)$match[1] * $units[$match[2]]) : null;
        }, 0);
    }

    public function checkExpiration(): void {
        $currentTime = time();
        foreach(['mutes', 'bans'] as $type) {
            foreach($this->{$type}->getAll() as $player => $data) {
                if($data['time'] <= $currentTime) {
                    $this->{$type}->remove($player);
                    $this->{$type}->save();
                    if($type === 'mutes' && ($target = Server::getInstance()->getPlayerExact($player))) {
                        $target->sendMessage("§aYour mute has expired");
                    }
                }
            }
        }
    }

    public function formatTime(int $seconds): string {
        $units = [86400 => 'd', 3600 => 'h', 60 => 'm', 1 => 's'];
        $result = [];
        foreach($units as $unit => $symbol) {
            if($count = floor($seconds / $unit)) {
                $result[] = $count . $symbol;
                $seconds %= $unit;
            }
        }
        return implode(' ', $result);
    }

    public function sendPlayerInfo(Player $staff, Player $user): void {
        $input = $this->getInputMode($user);
        $platform = $this->getDeviceOS($user);
        $device = $user->getPlayerInfo()->getExtraData()["DeviceModel"];

        $staff->sendMessage("Player Name: §a" . $user->getName());
        $staff->sendMessage(" ");
        $staff->sendMessage("§f- §7Device Model: §a" . $device);
        $staff->sendMessage("§f- §7Platform: §a" . $platform);
        $staff->sendMessage("§f- §7Player input: §a" . $input);
        $staff->sendMessage(" ");
    }

    public static function getInputMode(Player $player): string {
        $data = $player->getPlayerInfo()->getExtraData();
        return match ($data["CurrentInputMode"]) {
            InputMode::TOUCHSCREEN => "Touch",
            InputMode::MOUSE_KEYBOARD => "Keyboard",
            InputMode::GAME_PAD => "Controller",
            InputMode::MOTION_CONTROLLER => "Motion Controller",
            default => "Unknown"
        };
    }

    public static function getDeviceOS(Player $player): string {
        $data = $player->getPlayerInfo()->getExtraData();

        if ($data["DeviceOS"] === DeviceOS::ANDROID && $data["DeviceModel"] === "") {
            return "Linux";
        }

        return match ($data["DeviceOS"]) {
            DeviceOS::ANDROID => "Android",
            DeviceOS::IOS => "iOS",
            DeviceOS::OSX => "MacOS",
            DeviceOS::AMAZON => "FireOS",
            DeviceOS::GEAR_VR => "Gear VR",
            DeviceOS::HOLOLENS => "Hololens",
            DeviceOS::WINDOWS_10 => "Windows",
            DeviceOS::WIN32 => "WinEdu",
            DeviceOS::DEDICATED => "Dedicated",
            DeviceOS::TVOS => "TV OS",
            DeviceOS::PLAYSTATION => "PlayStation",
            DeviceOS::NINTENDO => "Nintendo Switch",
            DeviceOS::XBOX => "Xbox",
            DeviceOS::WINDOWS_PHONE => "Windows Phone",
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
}