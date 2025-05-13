<?php

namespace hcf\command;

use hcf\player\Player;
use muqsit\invmenu\InvMenu;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class CraftCommand extends Command{

    public function __construct(){
        parent::__construct("craft", "Menu de crafteo", "/craft");
        $this->setPermission("craft.command");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args){
        if (!$sender instanceof Player) return;

        if ($sender instanceof Player) {
            $inventory = InvMenu::create();
            $inventory->send($sender);
        }
    }
}