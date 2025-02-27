<?php

namespace hcf\abilities\entity;

use hcf\Loader;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\entity\Zombie;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;
use pocketmine\world\Position;
use hcf\abilities\items\PortableBard;
use pocketmine\item\VanillaItems;

class PortableBardEntity extends Zombie
{
    private $owner = null;
    private int $count_down = 50;//50 seconds
    private int $time = 0;
    private Position $pos;

    public function getName(): string
    {
        return "Bard";
    }

    public function spawnToAll(): void
    {
        parent::spawnToAll();
        $this->setMaxHealth(100);
        $this->setNameTagAlwaysVisible(true);
        $this->setCanSaveWithChunk(true);
        $owner = Loader::getInstance()->getServer()->getPlayerExact($this->owner);
        $this->setNameTag("§6§lPortable Bard\n§g§7: §f" . $owner->getName());
        $this->getArmorInventory()->setHelmet(VanillaItems::GOLDEN_HELMET());
        $this->getArmorInventory()->setChestplate(VanillaItems::GOLDEN_CHESTPLATE());
        $this->getArmorInventory()->setLeggings(VanillaItems::GOLDEN_LEGGINGS());
        $this->getArmorInventory()->setBoots(VanillaItems::GOLDEN_BOOTS());
    }

    public function onUpdate(int $currentTick): bool
    {
        if($this->time === 0 || time() - $this->time >= 1) {
            $this->time = time();
            if ($this->owner == null) {
                $this->close();
                return parent::onUpdate($currentTick);
            }
            $owner = Loader::getInstance()->getServer()->getPlayerExact($this->owner);
            foreach ($this->getWorld()->getNearbyEntities($this->getBoundingBox()->expandedCopy(10, 10, 10)) as $p) {
                if ($p instanceof Player) {
                    if ($p->getName() == $this->owner) {
                        if(PortableBard::isAllow($p)) {
                            if ($this->count_down <= (50) && $this->count_down >= (35)) {
                                $owner->getEffects()->add(new EffectInstance(VanillaEffects::RESISTANCE(), 20 * 2, 1));
                                $owner->getEffects()->add(new EffectInstance(VanillaEffects::REGENERATION(), 20 * 2, 1));
                            }
                            if ($this->count_down <= (35) && $this->count_down >= (20)) {
                                $owner->getEffects()->add(new EffectInstance(VanillaEffects::STRENGTH(), 20 * 2, 1));
                                $owner->getEffects()->add(new EffectInstance(VanillaEffects::REGENERATION(), 20 * 2, 2));
                            }
                            if ($this->count_down <= (20) && $this->count_down >= (10)) {
                                $owner->getEffects()->add(new EffectInstance(VanillaEffects::INVISIBILITY(), 20 * 2, 0));
                                $owner->getEffects()->add(new EffectInstance(VanillaEffects::STRENGTH(), 20 * 2, 1));
                            }
                            if ($this->count_down <= (10) && $this->count_down >= (5)) {
                                $owner->getEffects()->add(new EffectInstance(VanillaEffects::JUMP_BOOST(), 20 * 2, 7));
                            }
                            if ($this->count_down <= (5) && $this->count_down >= (0)) {
                                $owner->getEffects()->add(new EffectInstance(VanillaEffects::STRENGTH(), 20 * 2, 1));
                                $owner->getEffects()->add(new EffectInstance(VanillaEffects::REGENERATION(), 20 * 2, 1));
                            }
                        }
                    }
                }
            }
            if ($this->count_down > 0) {
                $this->count_down--;
            }
            if ($this->count_down <= 0) {
                $this->close();
            }
        }

        $this->teleport($this->pos);
        return parent::onUpdate($currentTick);
    }

    public function setOwner(Player $player)
    {
        $this->owner = $player->getName();
    }

    /**
     * @param Position $pos
     */
    public function setPos(Position $pos): void
    {
        $this->pos = $pos;
    }

    public function getOwner()
    {
        return $this->owner;
    }

}