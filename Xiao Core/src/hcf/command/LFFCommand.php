<?php

namespace hcf\command;

use hcf\Factory;
use hcf\player\Player;
use hcf\utils\inventorie\Inventories;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class LFFCommand extends Command
{
    public function __construct()
    {
        parent::__construct("lff", "Habre el menu del lff", "/lff");
        $this->setPermission("use.player.command");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (!$sender instanceof Player)
        return;

        if ($sender instanceof Player) {
            Factory::LFFMenu($sender);
        }
    }
}
