<?php

namespace unknown\query;

use pocketmine\scheduler\Task;
use unknown\Loader;

class QueryTask extends Task {

    public function onRun(): void {
        Loader::getInstance()->getQueryManager()->update();
    }
}
