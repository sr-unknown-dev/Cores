<?php

declare(strict_types=1);

namespace hcf\module\enchantment\defaults;

use hcf\module\enchantment\Enchantment;

use pocketmine\item\enchantment\ItemFlags;
use pocketmine\item\enchantment\Rarity;
use pocketmine\player\Player;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;

/**
 * Class GodEnchantment
 * @package hcf\module\enchantment\defaults
 */
class GodEnchantment extends Enchantment
{
    
    private $durationTicks = 0;

    /**
     * GodEnchantment construct.
     */
    public function __construct()
    {
        parent::__construct('God', Rarity::COMMON, ItemFlags::ARMOR, ItemFlags::NONE, 5);
    }
    
    /**
	 * @param Player $player
	 */
    public function handleMove(Player $player): void
    {
        if ($player->getHungerManager()->getFood() < $player->getHungerManager()->getMaxFood())
            $player->getHungerManager()->setFood($player->getHungerManager()->getMaxFood());
    }
    
    /**
     * @param Player $player
     */
    public function giveEffect(Player $player): void
    {
        $player->getEffects()->add(new EffectInstance(VanillaEffects::SPEED(), 2 * 20, 1, false, false));
        $player->getEffects()->add(new EffectInstance(VanillaEffects::STRENGTH(), 2 * 20, 0, false, false));
        $player->getEffects()->add(new EffectInstance(VanillaEffects::RESISTANCE(), 2 * 20, 0, false, false));
        $player->getEffects()->add(new EffectInstance(VanillaEffects::JUMP_BOOST(), 2 * 20, 1, false, false));
    }
}