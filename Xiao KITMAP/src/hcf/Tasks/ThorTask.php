<?php

namespace hcf\Tasks;

use hcf\player\Player;
use pocketmine\entity\Entity;
use pocketmine\scheduler\Task;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\network\mcpe\protocol\types\entity\PropertySyncData;
use pocketmine\world\Position;

class ThorTask extends Task
{
    private Player $player;
    private int $remainingTime;

    public function __construct(Player $player, int $duration = 10)
    {
        $this->player = $player;
        $this->remainingTime = $duration;
    }

    public function onRun(): void
    {
        if ($this->remainingTime > 0) {
            $this->spawnLightning();
            $this->remainingTime--;
        } else {
            $this->getHandler()?->cancel();
        }
    }

    private function spawnLightning(): void
    {
        $position = $this->player->getPosition();
        $runtimeId = Entity::nextRuntimeId();
        $location = $this->player->getLocation();

        $pk = AddActorPacket::create(
            $runtimeId,
            $runtimeId,
            EntityIds::LIGHTNING_BOLT,
            Position::fromObject($position, $position->getWorld()),
            null,
            $location->pitch,
            $location->yaw,
            $location->yaw,
            $location->yaw,
            [],
            [],
            new PropertySyncData([], []),
            []
        );

        $this->player->getNetworkSession()->sendDataPacket($pk);
    }
}