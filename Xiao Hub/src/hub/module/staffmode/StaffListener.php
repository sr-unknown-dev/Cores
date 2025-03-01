<?php

namespace hub\module\staffmode;

use hub\Loader;
use hub\player\Player;
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
        if (!$player instanceof Player || !$this->staffManager->bans->exists($player->getName())) return;

        $banData = $this->staffManager->bans->get($player->getName());
        if ($banData["time"] <= time()) {
            $this->staffManager->bans->remove($player->getName());
            $this->staffManager->bans->save();
            return;
        }

        $player->kick(TextFormat::colorize(
            "&7Estás baneado\n" .
            "Razón: &6{$banData['reazon']}\n" .
            "&7Expira en: " . $this->staffManager->formatTime($banData['time'] - time()) . "\n" .
            "&76Si deseas apelar el ban: &6" . Loader::getInstance()->getConfig()->get("discord-link")
        ));
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