<?php

namespace hcf\handler\knockback;

use pocketmine\event\Listener;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use hcf\Loader;
use hcf\player\Player;
use pocketmine\entity\Entity;
use pocketmine\scheduler\ClosureTask;

class KnockBackListener implements Listener {

    public function onEntityDamage(EntityDamageByEntityEvent $event): void {
        $entity = $event->getEntity();
        $atacante = $event->getDamager();
        $direccion = $entity->getPosition()->subtract($atacante->getPosition()->getX(), $atacante->getPosition()->getY(), $atacante->getPosition()->getZ())->normalize();
        $knockback = $direccion->multiply(Loader::getInstance()->getKnockBackManager()->getHorizontal());
        $knockback->y = Loader::getInstance()->getKnockBackManager()->getVertical();

        if ($entity instanceof Player && $atacante instanceof Entity) {
            $entity->setMotion($knockback);
        }
    }
}

