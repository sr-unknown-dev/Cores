<?php

namespace hcf\timer\Task;

use hcf\timer\types\TimerLoobox;
use Sakura\command\LootboxCommand;
use pocketmine\Server;

use pocketmine\scheduler\task;
use pocketmine\utils\TextFormat;

class LooboxTask extends task {
    
    /**
     * keytask Constructor.
     * @param Int $time
     */
    public function __construct(Int $time = 60){
        TimerLoobox::setTime($time);
    }
    
    /**
     * @param Int $currentTick
     * @return void
     */
    public function onRun() : void {
        if(!TimerLoobox::isEnable()){
            $this->getHandler()->cancel();
            return;
        }
        if (TimerLoobox::getTime() === 10) {
            foreach (Server::getInstance()->getOnlinePlayers() as $players) {
                $players->sendActionBarMessage(TextFormat::colorize("&gLibera 1 slot LootboxAll en 10seconds"));
            }
        }
        if(TimerLoobox::getTime() === 0){
            foreach (Server::getInstance()->getOnlinePlayers() as $player) {

                LootboxCommand::addPartner($player, 10);
            }
            TimerLoobox::setEnable(false);
            $this->getHandler()->cancel();
        }else{
            TimerLoobox::setTime(TimerLoobox::getTime() - 1);
        }
    }
}

?>