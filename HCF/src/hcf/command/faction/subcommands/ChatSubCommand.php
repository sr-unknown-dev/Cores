<?php

namespace hcf\command\faction\subcommands;

use CortexPE\Commando\BaseSubCommand;
use hcf\player\Player;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class ChatSubCommand extends BaseSubCommand
{

    public function __construct(string $name, string $description = "")
    {
        parent::__construct($name, $description);
    }

    protected function prepare(): void
    {
        $this->setPermission($this->getPermission());
    }

    public function getAliases(): array
    {
        return ["c"];
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender instanceof Player)
            return;
        
        if ($sender->getSession()->getFaction() === null) {
            $sender->sendMessage(TextFormat::colorize('&cYou don\'t have a faction'));
            return;
        }

        if ($sender->getSession()->hasFactionChat() === false) {
            $sender->getSession()->setFactionChat(true);
            $sender->sendMessage(TextFormat::GREEN . "You are now in the faction chat!");
        } else {
            $sender->getSession()->setFactionChat(false);
            $sender->sendMessage(TextFormat::RED . "You are now in public chat!");
        }
    }

    public function getPermission(): ?string
    {
        return "use.player.command";
    }
}