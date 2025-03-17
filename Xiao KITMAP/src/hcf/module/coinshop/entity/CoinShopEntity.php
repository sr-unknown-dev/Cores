<?php

declare(strict_types=1);

namespace hcf\module\coinshop\entity;

use hcf\player\Player;
use hcf\module\coinshop\utils\Utils;
use pocketmine\block\BlockIds;
use pocketmine\entity\Human;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\utils\TextFormat;

class CoinShopEntity extends Human
{

    public function initEntity(CompoundTag $nbt): void
    {
        parent::initEntity($nbt);
        $this->setForceMovementUpdate(false);
        $this->setNameTagAlwaysVisible(true);
        $this->setNameTag(TextFormat::colorize("§7\n§l§eCoinShop §eGlow!\n§7Crystals Custom 
        Store:§e store.glowmcpe.com
\n§b\n§7§oClick to see the mc store\n §7"));
    }

    public function attack(EntityDamageEvent $source): void
    {
        $source->cancel();

        if ($source instanceof EntityDamageByEntityEvent) {
            $damager = $source->getDamager();

            if ($damager instanceof Player) {
                if ($damager->hasPermission('remove.npc.coinshop') && $damager->getInventory()->getItemInHand()->getId() === BlockIds::BEDROCK) {
                    $this->kill();
                    return;
                }
                Utils::openCoinShop($damager);
            }
        }
    }
}
