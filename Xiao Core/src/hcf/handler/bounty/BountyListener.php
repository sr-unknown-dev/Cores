<?php

namespace hcf\handler\bounty;

use hcf\Loader;
use hcf\player\Player;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;

class BountyListener implements Listener
{

    public  function onDeath(PlayerDeathEvent $event):void
    {
        $player = $event->getPlayer();
        $target = $event->getEntity();

        if ($player instanceof Player && $target instanceof Player){
            if (Loader::getInstance()->bountyManager->hasBounty($target->getName())){
                Loader::getInstance()->getBountyManager()->claimBounty($target, $player);
            }
        }
    }
}