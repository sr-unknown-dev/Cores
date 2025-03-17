<?php

namespace hcf\handler\lootbox\commands\subcommands;

use CortexPE\Commando\BaseSubCommand;
use hcf\Loader;
use hcf\player\Player;
use pocketmine\command\CommandSender;

class EditSubCommand extends BaseSubCommand{
    public function __construct(string $name, string $description = "", array $aliases = [])
    {
        parent::__construct($name, $description, $aliases);
    }

    protected function prepare(): void
    {
        $this->setPermission($this->getPermission());
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender instanceof Player) return;

        Loader::getInstance()->getLootboxManager()->getLootbox()->EditMenu($sender);
    }

    public function getPermission(): ?string
    {
        return "lootbox.edit";
    }
}