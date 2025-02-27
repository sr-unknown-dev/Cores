<?php

namespace hcf\addons;

use hcf\addons\modules\AutoBrewing;
use hcf\addons\modules\AutoSmelting;
use hcf\addons\modules\CustomEnchantsEvents;
use hcf\addons\modules\DeathAnimation;
use hcf\addons\modules\HCFNether;
use hcf\addons\modules\JoinCommand;
use hcf\addons\modules\SwordStats;
use hcf\addons\modules\TemporalCobwebs;
use hcf\Loader;
use pocketmine\event\Listener;

final class AddonsManager implements Listener
{

    /**
     * @return void
     */
    public static function init(): void
    {
        Loader::getInstance()->getServer()->getPluginManager()->registerEvents(new JoinCommand(), Loader::getInstance());
        Loader::getInstance()->getServer()->getPluginManager()->registerEvents(new SwordStats(), Loader::getInstance());
        Loader::getInstance()->getServer()->getPluginManager()->registerEvents(new TemporalCobwebs(), Loader::getInstance());
         //Loader::getInstance()->getServer()->getPluginManager()->registerEvents(new AutoSmelting(), Loader::getInstance());
         //Loader::getInstance()->getServer()->getPluginManager()->registerEvents(new AutoBrewing(), Loader::getInstance());
         //Loader::getInstance()->getServer()->getPluginManager()->registerEvents(new CustomEnchantsEvents(), Loader::getInstance());
         //Loader::getInstance()->getServer()->getPluginManager()->registerEvents(new HCFNether(), Loader::getInstance());
    }
}