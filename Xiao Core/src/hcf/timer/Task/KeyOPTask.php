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
            foreach (Server::getInstance()->getOnlinePlayers() as $player) {
                    $starter = Loader::getInstance()->getHandlerManager()->getCrateManager()->getCrate("Starter");
                    $starter->giveKey($player, 70);
                    $ability = Loader::getInstance()->getHandlerManager()->getCrateManager()->getCrate("Ability");
                    $ability->giveKey($player, 40);
                    $partner = Loader::getInstance()->getHandlerManager()->getCrateManager()->getCrate("Partner");
                    $partner->giveKey($player, 30);
                    $staff = Loader::getInstance()->getHandlerManager()->getCrateManager()->getCrate("Staff");
                    $staff->giveKey($player, 30);
                    $ghostly = Loader::getInstance()->getHandlerManager()->getCrateManager()->getCrate("ghostly");
                    $ghostly->giveKey($player, 15);
                    $koth = Loader::getInstance()->getHandlerManager()->getCrateManager()->getCrate("Koth");
                    $koth->giveKey($player, 10);
                    $kits = Loader::getInstance()->getHandlerManager()->getCrateManager()->getCrate("Kits");
                    $kits->giveKey($player, 10);
            }
            TimerKeyOP::setEnable(false);
            $this->getHandler()->cancel();
        }else{
            TimerKeyOP::setTime(TimerKeyOP::getTime() - 1);
        }
    }
}

?>