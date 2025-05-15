<?php

namespace unknown\task;

use pocketmine\scheduler\Task;
use unknown\Loader;
use unknown\scoreboard\Scoreboard;

class ScoreboardTask extends Task {

    public function onRun(): void {
        Scoreboard::nextTick();

        foreach (Loader::getInstance()->getServer()->getOnlinePlayers() as $player) {
            Scoreboard::send($player);
        }
    }
}
