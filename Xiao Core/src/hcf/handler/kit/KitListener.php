<?php

declare(strict_types=1);

namespace hcf\handler\kit;

use hcf\Loader;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByChildEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\utils\TextFormat;

class KitListener implements Listener
{
    
    /**
     * @param EntityDamageEvent $event
     */
    public function handleDamage(EntityDamageEvent $event): void
    {
        if ($event->isCancelled())
            return;
        Loader::getInstance()->getHandlerManager()->getKitManager()->callEvent(__FUNCTION__, $event);
    }
    
    /**
     * @param EntityDamageByChildEntityEvent $event
     */
    public function handleDamageByChildEntity(EntityDamageByChildEntityEvent $event): void
    {
        if ($event->isCancelled())
            return;
        Loader::getInstance()->getHandlerManager()->getKitManager()->callEvent(__FUNCTION__, $event);
    }
    
    /**
     * @param PlayerItemHeldEvent $event
     */
    public function handleItemHeld(PlayerItemHeldEvent $event): void
    {
        if ($event->isCancelled())
            return;
        Loader::getInstance()->getHandlerManager()->getKitManager()->callEvent(__FUNCTION__, $event);
    }
    
    /**
     * @param PlayerItemUseEvent $event
     */
    public function handleItemUse(PlayerItemUseEvent $event): void
    {
        if ($event->isCancelled())
            return;
        Loader::getInstance()->getHandlerManager()->getKitManager()->callEvent(__FUNCTION__, $event);
    }

    public function handleInteract(PlayerInteractEvent $event): void {

        $player = $event->getPlayer();
    
        $item = $event->getItem();
    
        
    
        if(!KitsPortable::isPortable($item)) return;
    
        $event->cancel();
    
        
    
        $kitName = KitsPortable::getPortable($item);
    
        if($kitName === null) return;
    
        
    
        $kit = Loader::getInstance()->getHandlerManager()->getKitManager()->getKit($kitName);
    
        if($kit === null) return;
    
        
    
        $kit->giveTo($player);
    
        $item->setCount($item->getCount() - 1);
    
        $player->getInventory()->setItemInHand($item);
    
        $player->sendMessage(TextFormat::GREEN . "You have received the " . $kit->getNameFormat() . TextFormat::GREEN . " kit!");
    
    }
}