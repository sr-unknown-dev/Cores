<?php

/**
*     _    ___ ____  ____  ____   ___  ____  
*    / \  |_ _|  _ \|  _ \|  _ \ / _ \|  _ \ 
*   / _ \  | || |_) | | | | |_) | | | | |_) |
*  / ___ \ | ||  _ <| |_| |  _ <| |_| |  __/ 
* /_/   \_\___|_| \_\____/|_| \_\\___/|_|    
 */

namespace hcf\handler\airdrop;

use hcf\handler\airdrop\command\AirdropCommand;
use hcf\handler\KnockBack\KnockBack;
use hcf\Loader;

class AirdropManager
{
    public static $instance;

    public function __construct(){
        Loader::getInstance()->getServer()->getCommandMap()->register("airdrop", new AirdropCommand("airdrop", "Comando de airdrios"));
        Loader::getInstance()->getServer()->getPluginManager()->registerEvents(new AirdropListener(), Loader::getInstance());
        self::$instance = new Airdrops();
    }

    public static function getAirdrop(): Airdrops {
        return self::$instance;
    }
}