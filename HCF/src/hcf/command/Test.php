<?php

namespace TuPlugin;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;

class Test extends PluginBase {
    use SingletonTrait;

    public function onLoad(): void {
        parent::setInstance($this);
    }
}
