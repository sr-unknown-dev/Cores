<?php

declare(strict_types=1);

namespace hcf\handler\kit\classes\presets;

use hcf\handler\kit\classes\ClassFactory;
use hcf\handler\kit\classes\HCFClass;
use hcf\player\Player;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\utils\TextFormat;
use hcf\Loader;

/**
 * Class Mage
 * @package hcf\handler\kit\classes\presets
 */
class Mage extends HCFClass
{

    /**
     * Mage construct.
     */
    public function __construct()
    {
        parent::__construct(self::MAGE);
    }

    /**
     * @return Item[]
     */
    public function getArmorItems(): array
    {
        return [
            VanillaItems::GOLDEN_HELMET(),
            VanillaItems::CHAINMAIL_CHESTPLATE(),
            VanillaItems::CHAINMAIL_LEGGINGS(),
            VanillaItems::GOLDEN_BOOTS()
        ];
    }

    /**
     * @return EffectInstance[]
     */
    public function getEffects(): array
    {
        return ClassFactory::getClassById(self::ARCHER)->getEffects();
    }
    
    /**
     * @param PlayerItemUseEvent $event
     */
    public function handleItemUse(PlayerItemUseEvent $event): void
    {
        $player = $event->getPlayer();
        $item = $player->getInventory()->getItemInHand();

        if ($player instanceof Player) {
            if ($player->getClass() === null)
                return;
                
            if ($player->getClass()->getTypeId() === HCFClass::MAGE) {
                if ($player->getSession()->getEnergy('mage.energy') === null)
                    return;
                $energy = $player->getSession()->getEnergy('mage.energy');
                
                if ($player->getSession()->getCooldown('mage.cooldown') !== null)
                    return;
                    
                if ($player->getSession()->getCooldown('starting.timer') !== null || $player->getSession()->getCooldown('pvp.timer') !== null)
                    return;
            
                if ($player->getCurrentClaim() === 'Spawn')
                    return;
                    
                if ($item->getTypeId() === VanillaItems::AMETHYST_SHARD()->getTypeId()) {
                    if ($energy->getEnergy() < 35)
                        return;
                    
                    $player = $event->getPlayer();
  					$directionVector = $player->getDirectionVector();
  					$motion = $directionVector->multiply(Loader::getInstance()->getConfig()->get('amethyst.motion'));
  					$player->setMotion($motion);
                    
                    $item->pop();
                    $player->getInventory()->setItemInHand($item);
                    $player->getSession()->addCooldown('mage.cooldown', '&l&2Mage Effect&r&7: &r&c', 10);
                    $energy->reduceEnergy(35);
                } elseif ($item->getTypeId() === VanillaItems::FEATHER()->getTypeId()) {
                    if ($energy->getEnergy() < 35)
                        return;
                    
                    $launchPower = Loader::getInstance()->getConfig()->get('feather.motion'); // Cantidad de bloques a elevar
  					$motion = $player->getMotion();
  					$motion->y = $launchPower;
  					$player->setMotion($motion);
                    
                    $item->pop();
                    $player->getInventory()->setItemInHand($item);
                    $player->getSession()->addCooldown('mage.cooldown', '&l&2Mage Effect&r&7: &r&c', 10);
                    $energy->reduceEnergy(35);
                } elseif ($item->getTypeId() === VanillaItems::BLAZE_ROD()->getTypeId()) {
                    if ($energy->getEnergy() < 35)
                        return;
                    
                   	$player->getEffects()->add(new EffectInstance(VanillaEffects::SPEED(), 20 * 7, 1));
                    $players = array_filter($player->getServer()->getOnlinePlayers(), function ($target) use ($player): bool {
                         return $target instanceof Player && $player->getPosition()->distance($target->getPosition()) <= 20;
                    });
                       
                    if (count($players) !== 0) {
                        foreach ($players as $target) {
                            if ($target != $player) {
                                $target->getEffects()->add(new EffectInstance(VanillaEffects::WITHER(), 20 * 7, 1));
                                $target->sendMessage(TextFormat::colorize('&eThe Mage (&a' . $player->getName() . '&e) has used &4Wither II'));
                            }
                        }
                    }
                    $item->pop();
                    $player->getInventory()->setItemInHand($item);
                    $player->getSession()->addCooldown('mage.cooldown', '&l&2Mage Effect&r&7: &r&c', 10);
                    $energy->reduceEnergy(35);
                } elseif ($item->getTypeId() === VanillaItems::COAL()->getTypeId()) {
                    if ($energy->getEnergy() < 20)
                        return;
                    $player->getEffects()->add(new EffectInstance(VanillaEffects::SPEED(), 20 * 7, 3));
                    
                    $players = array_filter($player->getServer()->getOnlinePlayers(), function ($target) use ($player): bool {
                         return $target instanceof Player && $player->getPosition()->distance($target->getPosition()) <= 20;
                    });
                       
                    if (count($players) !== 0) {
                        foreach ($players as $target) {
                            if ($target != $player) {
                                $target->getEffects()->add(new EffectInstance(VanillaEffects::WITHER(), 20 * 7, 1));
                                $target->sendMessage(TextFormat::colorize('&eThe Mage (&a' . $player->getName() . '&e) has used &4Wither II'));
                            }
                        }
                    }
                    $item->pop();
                    $player->getInventory()->setItemInHand($item);
                    $player->getSession()->addCooldown('mage.cooldown', '&l&2Mage Effect&r&7: &r&c', 10);
                    $energy->reduceEnergy(20);
                }
            }
        }
    }
}