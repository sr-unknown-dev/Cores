<?php

declare(strict_types=1);

namespace hub\prefix\entity;

use hub\player\Player;
use hub\prefix\utils\Utils;
use pocketmine\block\VanillaBlocks;
use pocketmine\entity\Human;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\utils\TextFormat;

class PrefixEntity extends Human
{

    /**
     * @param CompoundTag $nbt
     */
    public function initEntity(CompoundTag $nbt): void
    {
        parent::initEntity($nbt);
        $this->setForceMovementUpdate(false);
        $this->setNameTagAlwaysVisible(true);
        $this->setNameTag(TextFormat::colorize("§7----------------------------------------\n§l§gPrefixes§aXiao!\n§bPrefixes - Tags\n§b\n§7§oClick to see the prefixes \n §7----------------------------------------"));
    }

    /**
     * @param EntityDamageEvent $source
     */
    public function attack(EntityDamageEvent $source): void
    {
        $source->cancel();

        if ($source instanceof EntityDamageByEntityEvent) {
            $damager = $source->getDamager();

            if ($damager instanceof Player) {
                if ($damager->hasPermission('remove.npc.prefix') && $damager->getInventory()->getItemInHand()->getTypeId() === VanillaBlocks::BEDROCK()->asItem()->getTypeId()) {
                    $this->kill();
                    return;
                }
                Utils::openPrefixMenu($damager);
            }
        }
    }
}