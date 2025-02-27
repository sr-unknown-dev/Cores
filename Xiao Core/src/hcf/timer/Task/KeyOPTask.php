<?php

namespace hcf\timer\Task;

use airdrops\utils\TextHelper;
use hcf\Loader;
use hcf\timer\types\TimerKeyOP;
use pocketmine\Server;

use pocketmine\scheduler\task;
use itoozh\crates\Main;
use pocketmine\utils\TextFormat;

class KeyOPTask extends task {
    
    /**
     * KEYALLtask Constructor.
     * @param Int $time
     */
    public function __construct(Int $time = 60){
        TimerKeyOP::setTime($time);
    }
    
    /**
     * @param Int $currentTick
     * @return void
     */
    public function onRun() : void {
        if(!TimerKeyOP::isEnable()){
            $this->getHandler()->cancel();
            return;
        }
        if (TimerKeyOP::getTime() === 10) {
            foreach (Server::getInstance()->getOnlinePlayers() as $players) {
                $players->sendActionBarMessage(TextFormat::colorize("&gLibera 7 slot OpKeyall en 10seconds"));
            }
        }
        if(TimerKeyOP::getTime() === 0){
            foreach (Server::getInstance()->getOnlinePlayers() as $player){
                foreach (Loader::getInstance()->getConfig()->get("events")["keyallop"]["keys"] as $name => $amount) {
                    $starter = Loader::getInstance()->getHandlerManager()->getCrateManager()->getCrate($name);
                    $starter->giveKey($player, $amount);
                }
            }
            TimerKeyOP::setEnable(false);
            $this->getHandler()->cancel();
        }else{
            TimerKeyOP::setTime(TimerKeyOP::getTime() - 1);
        }
    }
}

?>