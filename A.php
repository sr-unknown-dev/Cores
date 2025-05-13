<?php

namespace a;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\player\GameMode;

class A implements Listener
{
    public function handleFly(PlayerMoveEvent $event): void
    {
        $player = $event->getPlayer();

        if ($player->isFlying() && $player->getGamemode() !== GameMode::CREATIVE() || $player->getGamemode() !== GameMode::SPECTATOR())
    }
}