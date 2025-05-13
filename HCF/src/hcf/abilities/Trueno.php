<?php
namespace hcf\abilities;

use pocketmine\entity\Entity;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\network\mcpe\protocol\types\entity\PropertySyncData;
use pocketmine\player\Player;

trait Trueno
{

    public function sendLightning(Player $player):void
    {
        $position = $player->getPosition();
        $runtimeId = Entity::nextRuntimeId();

        $pk = AddActorPacket::create
        (
            $runtimeId,
            $runtimeId,
            EntityIds::LIGHTNING_BOLT,
            $position,
            null,
            $player->getLocation()->pitch,
            $player->getLocation()->yaw,
            $player->getLocation()->yaw,
            $player->getLocation()->yaw,
            [],
            [],
            new PropertySyncData([],[]),
            [],
        );

        $player->getNetworkSession()->sendDataPacket($pk);
    }
}