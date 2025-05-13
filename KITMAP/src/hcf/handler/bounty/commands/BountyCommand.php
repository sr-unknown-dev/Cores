<?php

namespace hcf\handler\bounty\commands;

use hcf\player\Player;
use hcf\utils\inventorie\Inventories;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class BountyCommand extends Command
{
    public function __construct()
    {
        parent::__construct("bounty", "Open Bounty Menu");
        $this->setPermission("use.player.command");
    }

    /**
     * @inheritDoc
     */
    public function execute(CommandSender $sender, string $label, array $args): void
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage(TextFormat::RED . "This command can only be used in-game.");
            return;
        }

        if (!$sender->hasPermission($this->getPermission())) {
            $sender->sendMessage(TextFormat::RED . "You do not have permission to use this command.");
            return;
        }

        Inventories::BountyMenu($sender);
    }
}