<?php

namespace hcf\module\anticheat\checks;

use hcf\Loader;
use hcf\player\Player;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerToggleFlightEvent;
use pocketmine\player\GameMode;

class Fly implements Listener {

    public function onToggleFlight(PlayerToggleFlightEvent $event): void {
        $player = $event->getPlayer();

        if ($player instanceof Player) {
            if (!$player->getServer()->isOp($player->getName()) && $player->getGamemode() !== GameMode::CREATIVE()) {
                if ($player->isFlying()) {
                    Loader::getInstance()->getAntiCheatManager()->AlertStaff($player, "Fly", 1);
                }
            }
        }
    }
}



