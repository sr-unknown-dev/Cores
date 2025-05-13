<?php

declare(strict_types=1);

namespace hcf\module\enchantment\defaults;

use hcf\module\enchantment\Enchantment;

use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\item\enchantment\Rarity;
use pocketmine\player\Player;

/**
 * Class FuryEnchantment
 * @package hcf\module\enchantment\defaults
 */
class FuryEnchantment extends Enchantment
{
    
    private $durationTicks = 0;

    /**
     * FuryEnchantment construct.
     */
    public function __construct()
    {
        parent::__construct('Fury', Rarity::COMMON, ItemFlags::ARMOR, ItemFlags::NONE, 2);
    }
    
    /**
     * @param Player $player
     */
    public function giveEffect(Player $player): void
    {
        $player->getEffects()->add(new EffectInstance(VanillaEffects::STRENGTH(), 10 * 20, 0, false, false));
    }
}