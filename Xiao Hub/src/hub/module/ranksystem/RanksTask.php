<?php

namespace hub\module\ranksystem;

use hub\Loader;
use pocketmine\scheduler\Task;

class RanksTask extends Task
{

    /**
     * @inheritDoc
     */
    public function onRun(): void {
        Loader::getInstance()->getRankManager()->checkExpiredRanks();
    }
}