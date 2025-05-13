<?php

namespace hcf\handler\bounty;

use hcf\Loader;
use hcf\player\Player;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;

class BountyListener implements Listener
{

    public function onDeath(PlayerDeathEvent $event): void
    {
        $player = $event->getPlayer();
        $cause = $player->getLastDamageCause();

        if ($cause instanceof EntityDamageByEntityEvent) {
            $killer = $cause->getDamager();

            if ($killer instanceof Player && $player instanceof Player) {
                if (Loader::getInstance()->getBountyManager()->hasBounty($player->getName())) {
                    Loader::getInstance()->getBountyManager()->claimBounty($player, $killer);
                }
            }
        }
    }
}