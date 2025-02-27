<?php

namespace daily;

use daily\Utils\Utils;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;

class Events implements Listener{

    public function handleJoin(PlayerJoinEvent $e):void{
        $player = $e->getPlayer();
        $name = $player->getName();
        $config = Utils::getConfig();

        if (!$config->exists($name)) {
            $config->set($name, ["time" => 0, "daily1" => false, "daily2" => false, "daily3" => false, "daily4" => false, "daily5" => false]);
            $config->save();
        }
    }
}