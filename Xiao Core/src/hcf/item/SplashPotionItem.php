<?php

declare(strict_types=1);

namespace hcf\item;

use hcf\entity\default\SplashPotionEntity;
use pocketmine\data\bedrock\PotionTypeIdMap;
use pocketmine\entity\Location;
use pocketmine\entity\projectile\Throwable;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemIds;
use pocketmine\item\PotionType;
use pocketmine\item\ProjectileItem;
use pocketmine\player\Player;
use pocketmine\item\ItemTypeIds;

class SplashPotionItem extends ProjectileItem
{
    
    /**
     * SplashPotionItem construct.
     * @param PotionType $type
     */
    public function __construct(
        private PotionType $type
    ) {
        parent::__construct(new ItemIdentifier(ItemTypeIds::SPLASH_POTION), $type->getDisplayName());//PotionTypeIdMap::getInstance()->toId($type)
    }
    
    /**
     * @return PotionType
     */
    public function getPotionType(): PotionType
    {
        return $this->type;
    }
    
    /**
     * @param Location $location
     * @param Player $thrower
     * @return Throwable
     */
    protected function createEntity(Location $location, Player $thrower): Throwable
    {
        return new SplashPotionEntity($location, $thrower, $this->type);
    }
    
    /**
     * @return float
     */
    public function getThrowForce(): float
    {
        return 0.5;
    }
    
    /**
     * @return int
     */
    public function getMaxStackSize(): int
    {
        return 1;
    }
}