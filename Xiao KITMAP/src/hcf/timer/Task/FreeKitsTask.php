<?php

namespace hcf\timer\Task;

use hcf\Loader;
use hcf\timer\types\TimerFreeKits;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class FreeKitsTask extends Task {

    public function __construct(Int $time = 60){
        TimerFreeKits::setTime($time);
    }
    
    /**
     * @param Int $currentTick
     * @return void
     */
    public function onRun() : void {
        if(!TimerFreeKits::isEnable()){
            $this->getHandler()->cancel();
            return;
        }
        if (TimerFreeKits::getTime() === 10) {
            foreach (Server::getInstance()->getOnlinePlayers() as $players) {
                $players->sendActionBarMessage(TextFormat::colorize("&gLibera 6 slot Keyall en 10seconds"));
            }
        }
        if(TimerFreeKits::getTime() === 0){
            foreach (Server::getInstance()->getOnlinePlayers() as $player) {
                $player->sendMessage(TextFormat::colorize("&a"));
            }
            TimerFreeKits::setEnable(false);
            $this->getHandler()->cancel();
        }else{
            TimerFreeKits::setTime(TimerFreeKits::getTime() - 1);
        }
    }
}