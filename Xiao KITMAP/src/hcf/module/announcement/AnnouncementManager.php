<?php

namespace hcf\module\announcement;

use hcf\Loader;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\TextFormat;

class AnnouncementManager {

    private $messages;

    private $currentId = 0;

    public function __construct(){
        $this->init();
        Loader::getInstance()->getScheduler()->scheduleRepeatingTask(new ClosureTask(function (): void {
            $message = Loader::getInstance()->getModuleManager()->getAnnouncementManager()->getNextMessage();
            Loader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize(Loader::getInstance()->getConfig()->get('prefix').$message));
        }), 5 * 60 * 20);
    }

    public function init(): void {
        $this->messages = Loader::getInstance()->getConfig()->get("messages");
    }

    /**
     * @return string
     */
    public function getNextMessage(): string {
        if(isset($this->messages[$this->currentId])) {
            $message = $this->messages[$this->currentId];
            $this->currentId++;
            return $message;
        }
        $this->currentId = 0;
        return $this->messages[$this->currentId];
    }
}