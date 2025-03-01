<?php

namespace hcf\timer\Task;

use airdrops\other\airdrop\AirDrop;
use airdrops\other\airdrop\AirDropFactory;
use airdrops\utils\TextHelper;
use hcf\Loader;
use hcf\timer\types\TimerAirdrop;
use hcf\handler\crate\CrateManager;
use hcf\player\Player;
use hcf\Factory;
use pocketmine\Server;

use pocketmine\scheduler\task;
use itoozh\crates\Main;
use pocketmine\block\Planks;
use pocketmine\utils\TextFormat;

class AirdropTask extends task {
    
    /**
     * keytask Constructor.
     * @param Int $time
     */
    public function __construct(Int $time = 60){
        TimerAirdrop::setTime($time);
    }
    
    /**
     * @param Int $currentTick
     * @return void
     */
    public function onRun() : void {
        if(!TimerAirdrop::isEnable()){
            $this->getHandler()->cancel();
            return;
        }
        if (TimerAirdrop::getTime() === 10) {
            foreach (Server::getInstance()->getOnlinePlayers() as $players) {
                $players->sendActionBarMessage(TextFormat::colorize("&gLibera 1 slot airdrop all en 10 seconds"));
            }
        }
        if(TimerAirdrop::getTime() === 0){
            $amount = 15;
            foreach (Server::getInstance()->getOnlinePlayers() as $players) {
                Factory::getAirdrop($players, $amount);
            }
            TimerAirdrop::setEnable(false);
            $this->getHandler()->cancel();
        }else{
            TimerAirdrop::setTime(TimerAirdrop::getTime() - 1);
        }
    }
}

?>