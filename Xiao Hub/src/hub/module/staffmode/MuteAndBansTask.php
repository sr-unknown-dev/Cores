<?php

namespace hub\module\staffmode;

use hub\Loader;
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