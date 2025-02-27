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
            foreach (Server::getInstance()->getOnlinePlayers() as $player) {
                $starter = Loader::getInstance()->getHandlerManager()->getCrateManager()->getCrate("Starter");
                $starter->giveKey($player, 35);
                $ability = Loader::getInstance()->getHandlerManager()->getCrateManager()->getCrate("Ability");
                $ability->giveKey($player, 20);
                $partner = Loader::getInstance()->getHandlerManager()->getCrateManager()->getCrate("Partner");
                $partner->giveKey($player, 15);
                $staff = Loader::getInstance()->getHandlerManager()->getCrateManager()->getCrate("Staff");
                $staff->giveKey($player, 15);
                $ghostly = Loader::getInstance()->getHandlerManager()->getCrateManager()->getCrate("ghostly");
                $ghostly->giveKey($player, 10);
                $koth = Loader::getInstance()->getHandlerManager()->getCrateManager()->getCrate("Koth");
                $koth->giveKey($player, 5);
                $kits = Loader::getInstance()->getHandlerManager()->getCrateManager()->getCrate("Kits");
                $kits->giveKey($player, 5);
            }
            TimerKey::setEnable(false);
            $this->getHandler()->cancel();
        }else{
            TimerKey::setTime(TimerKey::getTime() - 1);
        }
    }
}

?>