<?php

namespace hcf\module\ranksystem;

use hcf\Loader;
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