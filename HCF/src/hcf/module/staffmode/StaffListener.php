<?php

namespace hcf\module\staffmode;

use hcf\Loader;
use hcf\player\Player;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class StaffListener implements Listener {
    private StaffModeManager $staffManager;

    public function __construct() {
        $this->staffManager = Loader::getInstance()->getStaffModeManager();
    }

    public function onLogin(PlayerLoginEvent $event): void {
        $player = $event->getPlayer();
        if (!$player instanceof Player) return;

        $conn = $this->staffManager->bansDatabase->getConnection();
        $stmt = $conn->prepare("SELECT reason, expiration_time FROM bans WHERE player_name = ? AND (expiration_time > ? OR expiration_time = 0)");
        $name = $player->getName();
        $currentTime = time();
        $stmt->bind_param("si", $name, $currentTime);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $banData = $result->fetch_assoc();
            $player->kick(TextFormat::colorize(
                "&7Estás baneado\n" .
                "Razón: &6{$banData['reason']}\n" .
                "&7Expira en: " . $this->staffManager->formatTime($banData['expiration_time'] - time()) . "\n" .
                "&7Si deseas apelar el ban: &6" . Loader::getInstance()->getConfig()->get("discord-link")
            ));
        }
        $stmt->close();
    }

    public function onJoin(PlayerJoinEvent $event): void {
        $joiningPlayer = $event->getPlayer();
        foreach ($this->staffManager->getVanish() as $vanishedPlayer) {
            if ($vanishedPlayer instanceof Player) $joiningPlayer->hidePlayer($vanishedPlayer);
        }
    }

    public function onMove(PlayerMoveEvent $event): void {
        $player = $event->getPlayer();
        if ($player instanceof Player && $this->staffManager->isFreeze($player)) $event->cancel();
    }

    public function onChat(PlayerChatEvent $event): void {
        $player = $event->getPlayer();
        if (!$player instanceof Player) return;
        
        if ($this->staffManager->isMute($player)) {
            $event->cancel();
            return;
        }
        
        if ($this->staffManager->isStaffChat($player)) {
            foreach (Server::getInstance()->getOnlinePlayers() as $online) {
                if ($online->hasPermission("staff.perms") || $this->staffManager->isStaffChat($online)) {
                    $online->sendMessage(TextFormat::colorize("&8[&l&aStaffChat&r&8]&f: &b" . $player->getName() . " &f» " . $event->getMessage()));
                }
            }
            $event->cancel();
        }
    }

    public function onItemUse(PlayerItemUseEvent $event): void {
        $player = $event->getPlayer();
        if (!$player instanceof Player || !$player->hasPermission("staff.items")) return;
        
        $itemHand = $player->getInventory()->getItemInHand();

        if ($itemHand->getNamedTag()->getTag("staffs") && $itemHand->getNamedTag()->getString("staffs") === "teleport") {
            $player->sendForm(new PlayersForm);
            $event->cancel();
        }

        if ($itemHand->getNamedTag()->getTag("staffs") && $itemHand->getNamedTag()->getString("staffs") === "vanish") {
            Loader::getInstance()->getStaffModeManager()->toggleVanish($player);
            $event->cancel();
        }
    }

    public function onDamage(EntityDamageByEntityEvent $event): void {
        $player = $event->getEntity();
        $staff = $event->getDamager();
        
        if (!$staff instanceof Player || !$player instanceof Player || !$staff->hasPermission("staff.items")) return;
        
        $itemHand = $staff->getInventory()->getItemInHand();

        if ($itemHand->getNamedTag()->getTag("staffs") && $itemHand->getNamedTag()->getString("staffs") === "invsee") {
            Loader::getInstance()->getStaffModeManager()->invSee($staff, $player);
            $event->cancel();
        }

        if ($itemHand->getNamedTag()->getTag("staffs") && $itemHand->getNamedTag()->getString("staffs") === "freeze") {
            Loader::getInstance()->getStaffModeManager()->toggleFreeze($player);
            $event->cancel();
        }

        if ($itemHand->getNamedTag()->getTag("staffs") && $itemHand->getNamedTag()->getString("staffs") === "info") {
            Loader::getInstance()->getStaffModeManager()->sendPlayerInfo($staff, $player);
            $event->cancel();
        }
    }
}