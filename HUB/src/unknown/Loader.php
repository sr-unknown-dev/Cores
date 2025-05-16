<?php

namespace unknown;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use unknown\commands\ChatMuteCommand;
use unknown\commands\SetWhitelistStatusCommand;
use unknown\commands\UnMuteChatCommand;
use unknown\events\Events;
use unknown\query\QueryManager;
use unknown\query\QueryTask;
use unknown\rank\RankManage;
use unknown\scoreboard\ScoreboardManager;
use unknown\scoreboard\ScoreboardTask;

class Loader extends PluginBase
{
    public array $chatMute = [];
    public bool $chatMuteStatus = false;
    public ScoreboardManager $scoreboardManager;
    public QueryManager $queryManager;
    public RankManage $rankManage;
    use SingletonTrait;
    public function onLoad():void{self::setInstance($this);}

    protected function onEnable(): void
    {
        $this->scoreboardManager = new ScoreboardManager();
        $this->queryManager = new QueryManager();
        $this->rankManage = new RankManage();
        $this->getServer()->getPluginManager()->registerEvents(new Events(), $this);
        $this->getScheduler()->scheduleRepeatingTask(new ScoreboardTask(), 20);
        $this->getScheduler()->scheduleRepeatingTask(new QueryTask(), 100);
        $this->getServer()->getCommandMap()->registerAll('admin', [
            new ChatMuteCommand(),
            new SetWhitelistStatusCommand(),
            new UnMuteChatCommand()
        ]);

        $this->saveDefaultConfig();
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

    /**
     * @return RankManage
     */
    public function getRankManage(): RankManage
    {
        return $this->rankManage;
    }
}