<?php

namespace hcf\handler\package\commands\subcommands;

use CortexPE\Commando\BaseSubCommand;
use hcf\handler\package\PackageManager;
use hcf\Loader;
use hcf\player\Player;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class EditContentSubCommand extends BaseSubCommand
{
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
        if (!$sender->hasPermission("pkg.command")) {
            $sender->sendMessage(TextFormat::RED . "You don't have permissions");
            return;
        }
        
        if (!$sender instanceof Player) {
            $sender->sendMessage(TextFormat::RED . "This message can only be executed in game!");
            return;
        }
        $player = Loader::getInstance()->getServer()->getPlayerExact($sender->getName());
        PackageManager::getPartnerPackage()->sendMenu($sender);
        $sender->sendMessage(TextFormat::GREEN . "The content has been edited correctly");
    }

    public function getPermission(): ?string
    {
        return "package.edit";
    }
}