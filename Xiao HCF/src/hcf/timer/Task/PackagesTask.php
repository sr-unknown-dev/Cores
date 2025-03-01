<?php

namespace hcf\timer\Task;

use hcf\abilities\items\PartnerPackages;
use hcf\timer\types\TimerPackages;
use pocketmine\Server;

use pocketmine\scheduler\task;
use pocketmine\utils\TextFormat;

class PackagesTask extends task {
    
    /**
     * keytask Constructor.
     * @param Int $time
     */
    public function __construct(Int $time = 60){
        TimerPackages::setTime($time);
    }
    
    /**
     * @param Int $currentTick
     * @return void
     */
    public function onRun() : void {
        if(!TimerPackages::isEnable()){
            $this->getHandler()->cancel();
            return;
        }
        if (TimerPackages::getTime() === 10) {
            foreach (Server::getInstance()->getOnlinePlayers() as $players) {
                $players->sendActionBarMessage(TextFormat::colorize("&gLibera 1 slot PkgAll en 10seconds"));
            }
        }
        if(TimerPackages::getTime() === 0){
            foreach (Server::getInstance()->getOnlinePlayers() as $player) {

                PartnerPackages::addPartner($player, 20);
            }
            TimerPackages::setEnable(false);
            $this->getHandler()->cancel();
        }else{
            TimerPackages::setTime(TimerPackages::getTime() - 1);
        }
    }
}

?>