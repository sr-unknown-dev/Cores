<?php

namespace unknown;

use daily\Task\Tasks;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use unknown\events\Events;
use unknown\query\QueryManager;
use unknown\query\QueryTask;
use unknown\scoreboard\ScoreboardManager;
use unknown\task\ScoreboardTask;

class Loader extends PluginBase
{
    public array $chatMute = [];
    public bool $chatMuteStatus = false;
    public ScoreboardManager $scoreboardManager;
    public QueryManager $queryManager;
    use SingletonTrait;
    public function onLoad():void{self::setInstance($this);}

    protected function onEnable(): void
    {
        $this->scoreboardManager = new ScoreboardManager();
        $this->queryManager = new QueryManager();
        $this->getServer()->getPluginManager()->registerEvents(new Events(), $this);
        $this->getScheduler()->scheduleRepeatingTask(new ScoreboardTask(), 20);
        $this->getScheduler()->scheduleRepeatingTask(new QueryTask(), 100);
    }

    /**
     * @return ScoreboardManager
     */
    public function getScoreboardManager(): ScoreboardManager
    {
        return $this->scoreboardManager;
    }

    /**
     * @return QueryManager
     */
    public function getQueryManager(): QueryManager
    {
        return $this->queryManager;
    }
}