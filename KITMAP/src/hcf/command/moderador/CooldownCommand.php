<?php

namespace hcf\command\moderador;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class CooldownCommand extends Command
{
    private $cooldown = [];
    
    public function __construct()
    {
        parent::__construct("cooldown", "Coldoown de practica", "/cooldown");
        $this->setPermission("use.player.command");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (isset($this->cooldown[$sender->getName()]) && time() - $this->cooldown[$sender->getName()] < 10) {
            $sender->sendMessage("Tienes cooldown: ".time());
        }else{
            $sender->sendMessage("Holaaaaaaaa, se te añadio el cooldown");
            $this->cooldown[$sender->getName()] = time();
        }
    }
}
?>