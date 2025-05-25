<?php

namespace unknown\practice;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use unknown\practice\scoreboard\ScoreboardManager;
use unknown\practice\Utils\ranksystem\RankManager;

class Loader extends PluginBase
{
    use SingletonTrait;

    public ScoreboardManager $scoreboardManager;

    protected function onLoad(): void{self::setInstance($this);}

    protected function onEnable(): void
    {
        $this->scoreboardManager = new ScoreboardManager();
        $this->saveDefaultConfig();
        $this->saveResource("Arenas.yml");
    }

    /**
     * @return ScoreboardManager
     */
    public function getScoreboardManager(): ScoreboardManager
    {
        return $this->scoreboardManager;
    }
}