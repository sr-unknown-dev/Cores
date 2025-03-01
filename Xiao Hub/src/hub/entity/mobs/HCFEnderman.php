<?php

declare(strict_types=1);

namespace juqn\hub\entity;

use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Living;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataFlags;
use pocketmine\player\Player;

/**
 * Class TextEntity
 * @package juqn\hub\entity
 */
class HCFEnderman extends Living {

    protected float $jumpVelocity = 0.5;
    private float $speed = 0.3;
    public $attackdelay;

    protected function getInitialSizeInfo(): EntitySizeInfo {
        return new EntitySizeInfo(2.9, 0.6, 2.5);
    }

    public static function getNetworkTypeId(): string {
        return EntityIds::ENDERMAN;
    }

    public function getName(): string {
        return "Enderman";
    }

    protected function initEntity(CompoundTag $nbt): void {
        $this->setMaxHealth(40);
        $this->setHealth(40);
        $this->setCanSaveWithChunk(false);
        $this->attackdelay = 0;
        parent::initEntity($nbt);
    }

    protected function entityBaseTick(int $tickDiff = 1): bool {
        if($this->closed) return false;

        $hasUpdate = parent::entityBaseTick($tickDiff);

        if(!$this->isAlive()) return $hasUpdate;

        $nearest = $this->location->world->getNearestEntity($this->location, 15, Player::class);
        if($nearest === null) return $hasUpdate;

        $this->lookAt($nearest->getEyePos());

        $this->setAttackDelay($this->getAttackDelay() + 1);

        $dist = $this->getPosition()->distanceSquared($nearest->getPosition());

        if ($this->getAttackDelay() > 60 && $dist < 4) {
            $this->setAttackDelay(0);
            $ev = new EntityDamageByEntityEvent($this, $nearest, EntityDamageEvent::CAUSE_ENTITY_ATTACK, 5);
            $nearest->attack($ev);
        }

        if($this->isCollidedHorizontally) $this->jump();
        if($nearest->location->distance($this->location) > 2 && $this->isCollided){
            $mVec = $this->getDirectionVector()->multiply($this->speed);
            $mVec->y = 0;
            $this->motion = $this->motion->addVector($mVec);
        }

        return $hasUpdate;
    }

    public function lookAt(Vector3 $target) : void{
        $horizontal = sqrt(($target->x - $this->location->x) ** 2 + ($target->z - $this->location->z) ** 2);
        $vertical = $target->y - ($this->location->y + $this->getEyeHeight());
        $this->location->pitch = -atan2($vertical, $horizontal) / M_PI * 180; //negative is up, positive is down

        $xDist = $target->x - $this->location->x;
        $zDist = $target->z - $this->location->z;
        $this->location->yaw = atan2($zDist, $xDist) / M_PI * 180 - 90;
        if($this->location->yaw < 0){
            $this->location->yaw += 360.0;
        }
    }

    public function getXpDropAmount(): int {
        return 5;
    }

    public function getDrops(): array {
        return lcg_value() > 0.5 ? [VanillaItems::ENDER_PEARL()] : [];
    }

    public function attack(EntityDamageEvent $source): void {
        if($source instanceof EntityDamageByEntityEvent){
            $this->getNetworkProperties()->setGenericFlag(EntityMetadataFlags::ANGRY, true);
            $this->speed = 0.45;
        }
        parent::attack($source);
    }

    public function setAttackDelay(int $attackdelay) {
        $this->attackdelay = $attackdelay;
    }

    public function getAttackDelay() {
        return $this->attackdelay;
    }

}