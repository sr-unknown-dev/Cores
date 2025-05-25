<?php

namespace unknown\practice\scoreboard;

use pocketmine\scheduler\Task;
use unknown\Loader;
use unknown\practice\scoreboard\Scoreboard;

class ScoreboardTask extends Task {

    public function onRun(): void {
        Scoreboard::nextTick();

        foreach (Loader::getInstance()->getServer()->getOnlinePlayers() as $player) {
            Scoreboard::send($player);
        }
    }
}
