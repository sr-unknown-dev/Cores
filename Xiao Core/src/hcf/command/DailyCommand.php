<?php

namespace hcf\command;


use hcf\player\Player;
use hcf\utils\inventorie\Inventories;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class DailyCommand extends Command
{
    public function __construct()
    {
        parent::__construct("daily", "Habre el menu de los daily", "/daily");
        $this->setPermission("use.player.command");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (!$sender instanceof Player)
            return;
        
        Inventories::DailyMenu($sender);
    }
}