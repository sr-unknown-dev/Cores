<?php

namespace hcf\handler\package;

use hcf\Loader;
use hcf\handler\package\PartnerPackage;
use hcf\handler\package\commands\PartnerPackagesCommand;
use pocketmine\utils\SingletonTrait;

class PackageManager {
    public static $instance;

    public function __construct(){
        Loader::getInstance()->getServer()->getCommandMap()->register("pkg", new PartnerPackagesCommand("pkg", "Comandos de Partner Packages"));
        self::$instance = new PartnerPackage();
    }

    public static function getPartnerPackage(): PartnerPackage {
        return self::$instance;
    }
}

