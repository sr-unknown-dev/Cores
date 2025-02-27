<?php

namespace hcf\timer\Task;

use airdrops\other\airdrop\AirDrop;
use airdrops\other\airdrop\AirDropFactory;
use airdrops\utils\TextHelper;
use hcf\Loader;
use hcf\handler\crate\CrateManager;
use hcf\player\Player;
use hcf\timer\types\TimerMystery;
use pocketmine\Server;

use pocketmine\scheduler\task;
use itoozh\mystery\Main;
use pocketmine\block\Planks;
use pocketmine\utils\TextFormat;

class MysteryTask extends task {
    
    /**
     * keytask Constructor.
     * @param Int $time
     */
    public function __construct(Int $time = 60){
        TimerMystery::setTime($time);
    }
    
    /**
     * @param Int $currentTick
     * @return void
     */
    public function onRun() : void {
        if(!TimerMystery::isEnable()){
            $this->getHandler()->cancel();
            return;
        }
        if (TimerMystery::getTime() === 10) {
            foreach (Server::getInstance()->getOnlinePlayers() as $players) {
                $players->sendActionBarMessage(TextFormat::colorize("&gLibera 1 slot MysteryAll en 10seconds"));
            }
        }
        if(TimerMystery::getTime() === 0){
            $amount = 15;
            $item = Main::getMysteryCrateItem($amount);
            foreach (Server::getInstance()->getOnlinePlayers() as $players) {
                $players->getInventory()->addItem($item);
            }
            TimerMystery::setEnable(false);
            $this->getHandler()->cancel();
        }else{
            TimerMystery::setTime(TimerMystery::getTime() - 1);
        }
    }
}

?>