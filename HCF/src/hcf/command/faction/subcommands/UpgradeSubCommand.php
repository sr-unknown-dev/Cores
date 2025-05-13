<?php

namespace hcf\command\faction\subcommands;

use CortexPE\Commando\BaseSubCommand;
use hcf\player\Player;
use hcf\utils\upgrades\UpgradeMenu;
use pocketmine\command\CommandSender;

class UpgradeSubCommand extends BaseSubCommand
{

    /**
     * @inheritDoc
     */
    protected function prepare(): void
    {
        $this->setPermission("use.player.command");
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender instanceof Player)
            return;

        UpgradeMenu::create($sender);
    }
}