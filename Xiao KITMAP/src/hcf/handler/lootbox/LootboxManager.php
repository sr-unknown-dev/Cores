<?php

namespace hcf\handler\lootbox;

use hcf\handler\lootbox\commands\LootboxCommand;
use hcf\Loader;

class LootboxManager{

    public $instance;

    public function __construct(){
        $this->instance = new Lootbox();
        Loader::getInstance()->getServer()->getCommandMap()->register("lootbox", new LootboxCommand("lootbox", "Lootbox commands"));
        Loader::getInstance()->getServer()->getPluginManager()->registerEvents(new LootboxListener(), Loader::getInstance());
    }

    public function getLootbox(): Lootbox {
        return $this->instance;
    }
}