<?php

namespace unknown;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;

class Loader extends PluginBase
{
    use SingletonTrait;
    protected function onLoad(): void
    {
        self::setInstance($this);
    }

    public function onEnable(): void
    {
        $this->getLogger()->info("Plugin enabled");
        $this->saveDefaultConfig();
        $this->getServer()->getPluginManager()->registerEvents(new events\Events(), $this);
    }
}