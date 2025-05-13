<?php

namespace hcf\abilities\entity;

use pocketmine\entity\Zombie;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;
use pocketmine\world\Position;

class Cerdito1Entity extends Zombie
{
    private $owner = null;
    private int $count_down = 5;//5 seconds
    private int $time = 0;
    private Position $pos;

    public function getName(): string
    {
        return "Pig";
    }

    public function spawnToAll(): void
    {
        parent::spawnToAll();
        $this->setMaxHealth(100);
        $this->setNameTagAlwaysVisible(true);
        $this->setCanSaveWithChunk(true);
        $this->setNameTag("§6§lStrength");
    }

    public function onUpdate(int $currentTick): bool
    {
        if($this->time === 0 || time() - $this->time >= 1) {
            $this->time = time();
            if ($this->owner == null) {
                $this->close();
                return parent::onUpdate($currentTick);
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