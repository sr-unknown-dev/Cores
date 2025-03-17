<?php

namespace hcf\module\staffmode;

use hcf\Loader;
use pocketmine\scheduler\Task;

class MuteAndBansTask extends Task
{

    /**
     * @inheritDoc
     */
    public function onRun(): void
    {
        Loader::getInstance()->getStaffModeManager()->checkExpiration();
    }
}