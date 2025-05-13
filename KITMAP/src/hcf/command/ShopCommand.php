<?php

namespace hcf\command;

use hcf\module\blockshop\utils\ShopAndSell;
use hcf\player\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class ShopCommand extends Command
{
    public function __construct()
    {
        parent::__construct("shop", "Menu de shop", "/shop");
        $this->setPermission("use.player.command");
    }

    /**
     * @inheritDoc
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (!$sender instanceof Player) return;

        ShopAndSell::Shop($sender);
    }
}