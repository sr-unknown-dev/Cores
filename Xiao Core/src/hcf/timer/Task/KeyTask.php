<?php

namespace hcf\timer\Task;

use hcf\Loader;
use hcf\timer\types\TimerKey;
use hcf\handler\crate\CrateManager;
use pocketmine\Server;

use pocketmine\scheduler\task;
use itoozh\crates\Main;
use pocketmine\utils\TextFormat;

class KeyTask extends task {
    
    /**
     * keytask Constructor.
     * @param Int $time
     */
    public function __construct(Int $time = 60){
        TimerKey::setTime($time);
    }
    
    /**
     * @param Int $currentTick
     * @return void
     */
    public function onRun() : void {
        if(!TimerKey::isEnable()){
            $this->getHandler()->cancel();
            return;
        }
        if (TimerKey::getTime() === 10) {
            foreach (Server::getInstance()->getOnlinePlayers() as $players) {
                $players->sendActionBarMessage(TextFormat::colorize("&gLibera 6 slot Keyall en 10seconds"));
            }
        }
        if(TimerKey::getTime() === 0){
            foreach (Server::getInstance()->getOnlinePlayers() as $player){
            foreach (Loader::getInstance()->getConfig()->get("events")["keyall"]["keys"] as $name => $amount) {
                $starter = Loader::getInstance()->getHandlerManager()->getCrateManager()->getCrate($name);
                $starter->giveKey($player, $amount);
            }
        }
            TimerKey::setEnable(false);
            $this->getHandler()->cancel();
        }else{
            TimerKey::setTime(TimerKey::getTime() - 1);
        }
    }
}

?>