<?php
namespace hcf\abilities;

use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\network\mcpe\protocol\types\entity\PropertySyncData;
use pocketmine\player\Player;

class Hits extends Player{

    private $data;

    public function register():void{
        $this->data[$this->getName()] = 1;
    }

    public function remove():void{
        unset($this->data[$this->getName()]);
    }

    public function has():bool{
        return isset($this->data[$this->getName()]);
    }

    public function add():void
    {
        if(!$this->has())$this->register();

        $this->data[$this->getName()]++;
        if($this->data[$this->getName()] < 3)return;
    }

    public function sendLightning(Player $player):void
    {
        $position = $player->getPosition();
        $runtimeId = $player->getId();

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