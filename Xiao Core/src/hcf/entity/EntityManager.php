<?php

declare(strict_types=1);

namespace hcf\entity;

use hcf\entity\default\EnderpearlEntity;
use hcf\entity\default\SplashPotionEntity;
use hcf\entity\projectiles\FireworksRocket;
use hcf\entity\server\AbilityEntity;
use hcf\entity\server\BountyEntity;
use hcf\entity\server\DailyEntity;
use hcf\entity\server\FixallEntity;
use hcf\entity\server\GkitEntity;
use hcf\entity\server\InfoEntity;
use hcf\entity\server\SupportEntity;
use hcf\entity\tops\TopFactionsEntity;
use hcf\entity\tops\TopKDREntity;
use hcf\entity\tops\TopKillsEntity;
use pocketmine\data\SavedDataLoadingException;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\entity\object\ItemEntity;
use pocketmine\item\Item;
use pocketmine\item\PotionType;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\world\World;

/**
 * Class EntityManager
 * @package hcf\entity
 */
class EntityManager
{

    /**
     * EntityManager construct.
     */
    public function __construct()
    {
        EntityFactory::getInstance()->register(CustomItemEntity::class, function(World $world, CompoundTag $nbt) : ItemEntity{
            $itemTag = $nbt->getCompoundTag("Item");
            if($itemTag === null){
                throw new SavedDataLoadingException("Expected \"Item\" NBT tag not found");
            }

            $item = Item::nbtDeserialize($itemTag);
            if($item->isNull()){
                throw new SavedDataLoadingException("Item is invalid");
            }
            return new CustomItemEntity(EntityDataHelper::parseLocation($nbt, $world), $item, $nbt);
        }, ['Item', 'minecraft:item'], EntityIds::ITEM);

        EntityFactory::getInstance()->register(TextEntity::class, function (World $world, CompoundTag $nbt): TextEntity {
            return new TextEntity(EntityDataHelper::parseLocation($nbt, $world), $nbt);
        }, ['TextEntity', 'minecraft:textentity'], EntityIds::BAT);

        EntityFactory::getInstance()->register(EnderpearlEntity::class, function(World $world, CompoundTag $nbt): EnderpearlEntity {
            return new EnderpearlEntity(EntityDataHelper::parseLocation($nbt, $world), null, $nbt);
        }, ['ThrownEnderpearl', 'minecraft:ender_pearl'], EntityIds::ENDER_PEARL);
        EntityFactory::getInstance()->register(SplashPotionEntity::class, function(World $world, CompoundTag $nbt): SplashPotionEntity {
            return new SplashPotionEntity(EntityDataHelper::parseLocation($nbt, $world), null, PotionType::STRONG_HEALING(), $nbt);
        }, ['ThrownPotion', 'minecraft:splash_potion'], EntityIds::SPLASH_POTION);

        EntityFactory::getInstance()->register(AbilityEntity::class, function (World $world, CompoundTag $nbt): AbilityEntity {
            return new AbilityEntity(EntityDataHelper::parseLocation($nbt, $world), AbilityEntity::parseSkinNBT($nbt), $nbt);
        }, ['AbilityEntity']);

        EntityFactory::getInstance()->register(InfoEntity::class, function (World $world, CompoundTag $nbt): InfoEntity {
            return new InfoEntity(EntityDataHelper::parseLocation($nbt, $world), InfoEntity::parseSkinNBT($nbt), $nbt);
        }, ['InfoEntity']);

        EntityFactory::getInstance()->register(SupportEntity::class, function (World $world, CompoundTag $nbt): SupportEntity {
            return new SupportEntity(EntityDataHelper::parseLocation($nbt, $world), SupportEntity::parseSkinNBT($nbt), $nbt);
        }, ['SupportEntity']);

        EntityFactory::getInstance()->register(BountyEntity::class, function (World $world, CompoundTag $nbt): BountyEntity {
            return new BountyEntity(EntityDataHelper::parseLocation($nbt, $world), BountyEntity::parseSkinNBT($nbt), $nbt);
        }, ['BountyEntity']);

        EntityFactory::getInstance()->register(FixallEntity::class, function (World $world, CompoundTag $nbt): FixallEntity {
            return new FixallEntity(EntityDataHelper::parseLocation($nbt, $world), FixallEntity::parseSkinNBT($nbt), $nbt);
        }, ['FixallEntity']);

        EntityFactory::getInstance()->register(BountyEntity::class, function (World $world, CompoundTag $nbt): BountyEntity {
            return new BountyEntity(EntityDataHelper::parseLocation($nbt, $world), BountyEntity::parseSkinNBT($nbt), $nbt);
        }, ['BountyEntity']);

        EntityFactory::getInstance()->register(GkitEntity::class, function (World $world, CompoundTag $nbt): GkitEntity {
            return new GkitEntity(EntityDataHelper::parseLocation($nbt, $world), GkitEntity::parseSkinNBT($nbt), $nbt);
        }, ['GkitEntity']);

        EntityFactory::getInstance()->register(TopKillsEntity::class, function (World $world, CompoundTag $nbt): TopKillsEntity {
            return new TopKillsEntity(EntityDataHelper::parseLocation($nbt, $world), TopKillsEntity::parseSkinNBT($nbt), $nbt);
        }, ['TopKillsEntity']);

        EntityFactory::getInstance()->register(TopKDREntity::class, function (World $world, CompoundTag $nbt): TopKDREntity {
            return new TopKDREntity(EntityDataHelper::parseLocation($nbt, $world), TopKDREntity::parseSkinNBT($nbt), $nbt);
        }, ['TopKDREntity']);

        EntityFactory::getInstance()->register(TopFactionsEntity::class, function (World $world, CompoundTag $nbt): TopFactionsEntity {
            return new TopFactionsEntity(EntityDataHelper::parseLocation($nbt, $world), TopFactionsEntity::parseSkinNBT($nbt), $nbt);
        }, ['TopFactionsEntity']);

        EntityFactory::getInstance()->register(FireworksRocket::class, static function (World $world, CompoundTag $nbt): FireworksRocket {
            return new FireworksRocket(EntityDataHelper::parseLocation($nbt, $world), Item::nbtDeserialize($nbt->getCompoundTag("Item")));
        }, ["FireworksRocket", EntityIds::FIREWORKS_ROCKET]);
    }
}