<?php

declare(strict_types=1);

namespace juqn\hcf\entity;

use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Living;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataFlags;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\world\particle\HugeExplodeParticle;
use pocketmine\world\sound\ExplodeSound;

/**
 * Class TextEntity
 * @package juqn\hcf\entity
 */
class HCFCreeper extends Living {

    protected float $jumpVelocity = 0.1;
    private float $speed = 0.1;
    public $attackdelay;

    protected function getInitialSizeInfo(): EntitySizeInfo {
        return new EntitySizeInfo(1.7, 0.6, 1.3);
    }

    public static function getNetworkTypeId(): string {
        return EntityIds::CREEPER;
    }

    public function getName(): string {
        return "Creeper";
    }

    protected function initEntity(CompoundTag $nbt): void {
        $this->setMaxHealth(20);
        $this->setHealth(20);
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

        $dist = $this->getPosition()->distanceSquared($nearest->getPosition());
        if ($dist > 5) {
            $this->setAttackDelay(0);
        }
        if ($dist < 3.9) {
            $this->setAttackDelay($this->getAttackDelay() + 1);
        }

        if ($this->getAttackDelay() > 20 && $dist < 3) {
            $this->setAttackDelay(0);
            foreach (Server::getInstance()->getOnlinePlayers() as $online_player) {
                if ($this->getPosition()->distance($online_player->getPosition()) <= 5) {
                    $ev = new EntityDamageByEntityEvent($this, $online_player, EntityDamageEvent::CAUSE_ENTITY_EXPLOSION, 5);
                    $online_player->attack($ev);
                }
            }
            $world = $this->getWorld();
            $world->addSound($this->location, new ExplodeSound());
            $world->addParticle($this->getPosition(), new HugeExplodeParticle(), [$nearest]);
            $this->flagForDespawn();
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
        return lcg_value() > 0.5 ? [VanillaItems::GUNPOWDER()] : [];
    }

    public function attack(EntityDamageEvent $source): void {
        if($source instanceof EntityDamageByEntityEvent){
            $this->getNetworkProperties()->setGenericFlag(EntityMetadataFlags::ANGRY, true);
            $this->speed = 0.1;
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