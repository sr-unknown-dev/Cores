<?php

namespace hcf\Tasks;

use hcf\Loader;
use hcf\player\Player;
use pocketmine\scheduler\Task;
use pocketmine\utils\TextFormat;

class InviteTask extends Task
{

    private Player $player;
    private Player $target;
    private int $time = 60;


    public function __construct(Player $player, Player $target)
    {
        $this->player = $player;
        $this->target = $target;
    }

    /**
     * @inheritDoc
     */
    public function onRun(): void
    {
        if ($this->time < 0) {
            if (!$this->player->isOnline()) {
                return;
            }else{
                $this->player->sendMessage(TextFormat::RED."La invitación de ".$this->target->getName()." ha expirado");
                $this->getHandler()->cancel();
            }

            if (!$this->target->isOnline()){
                return;
            }else{
                $this->target->sendMessage(TextFormat::RED."La invitación de ".$this->player->getSession()->getFaction()." ha expirado");
                $this->getHandler()->cancel();
            }
        }
        $this->time--;
    }
}