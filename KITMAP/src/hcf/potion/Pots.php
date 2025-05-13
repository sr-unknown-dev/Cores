<?php

namespace hcf\potion;

use hcf\player\Player;
use pocketmine\entity\projectile\SplashPotion;
use pocketmine\event\Listener;
use pocketmine\item\PotionType;
use pocketmine\event\entity\ProjectileHitBlockEvent;

class Pots implements Listener
{
    public function onHit(ProjectileHitBlockEvent $event){
        $projectile = $event->getEntity();
        if($projectile instanceof SplashPotion && $projectile->getPotionType() === PotionType::STRONG_HEALING()){
            $player = $projectile->getOwningEntity();
            if($player instanceof Player && $player->isAlive() && $projectile->getPosition()->distance($player->getPosition()) <= 15){
                $player->setHealth($player->getHealth() + 6);
            }
        }
    }
}