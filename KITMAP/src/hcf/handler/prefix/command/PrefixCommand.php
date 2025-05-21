<?php

namespace hcf\handler\prefix\command;

use CortexPE\Commando\BaseCommand;
use hcf\handler\prefix\command\subcommands\RemoveNpcPrefixSubCommand;
use hcf\handler\prefix\command\subcommands\RemovePrefixSubCommand;
use hcf\handler\prefix\command\subcommands\SetNpcPrefixSubCommand;
use hcf\handler\prefix\command\subcommands\SetPrefixCommand;
use hcf\handler\prefix\command\subcommands\SetPrefixSubCommand;
use hcf\Loader;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class PrefixCommand extends BaseCommand
{
    public function __construct()
    {
        parent::__construct(Loader::getInstance(), "prefix", "Prefix Commands");
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage("This command can only be used in-game.");
        }

        if ($sender instanceof Player) {
            $sender->sendMessage("Prefix CommanUd");
            $sender->sendMessage("/prefix setprefix <prefix> <player>\n/prefix removeprefix <player>\n/prefix list\n/prefix setnpc\n/prefix removenpc");
        }
    }

    protected function prepare(): void
    {
        $this->setPermission($this->getPermission());
        $this->registerSubCommand(new SetPrefixSubCommand());
        $this->registerSubCommand(new RemovePrefixSubCommand());
        $this->registerSubCommand(new SetNpcPrefixSubCommand());
        $this->registerSubCommand(new RemoveNpcPrefixSubCommand());
    }

    public function getPermission(): ?string
    {
        return "prefix.admin";
    }
}