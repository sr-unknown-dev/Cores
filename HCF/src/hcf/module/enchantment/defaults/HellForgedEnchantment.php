<?php

declare(strict_types=1);

namespace hcf\module\enchantment\defaults;

use hcf\module\enchantment\Enchantment;
use hcf\utils\Utils;
use pocketmine\entity\projectile\Snowball;
use pocketmine\item\Armor;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\item\enchantment\Rarity;
use pocketmine\player\Player;

/**
 * Class ImplantsEnchantment
 * @package hcf\module\enchantment\defaults
 */
class HellForgedEnchantment extends Enchantment
{
    
    private $durationTicks = 0;

    /**
     * ImplantsEnchantment construct.
     */
    public function __construct()
    {
        parent::__construct('HellForged', Rarity::COMMON, ItemFlags::ARMOR, ItemFlags::NONE, 5);
    }
    
    /**
	 * @param Player $player
	 */
    public function handleMove(Player $player): void
    {
        foreach ($player->getArmorInventory()->getContents() as $slot => $item) {
            /** @var Armor $item */
            if($item instanceof Armor) {
                if ($item->getDamage() > 0) {
                    $player->getArmorInventory()->setItem($slot, $item->setDamage($item->getDamage() - 1));
                }
            }

            $player->getNetworkSession()->sendDataPacket(Utils::addParticle($player->getPosition()->add(0, -1.3, 0), "minecraft:end_chest"));
        }
    }
}