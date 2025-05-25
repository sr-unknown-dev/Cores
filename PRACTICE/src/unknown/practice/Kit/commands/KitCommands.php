<?php

namespace unknown\practice\Kit\commands;

use CortexPE\Commando\BaseCommand;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat;
use unknown\practice\Kit\commands\subcommands\CreateSubCommand;
use unknown\practice\Loader;

class KitCommands extends BaseCommand
{
    public function __construct()
    {
        parent::__construct(
            Loader::getInstance(),
            'kits',
            'kits commands'
        );
    }

    /**
     * @inheritDoc
     */
    protected function prepare(): void
    {
        $this->setPermission($this->getPermission());
        $this->registerSubCommand(new CreateSubCommand());
        $this->registerSubCommand();
        $this->registerSubCommand();
    }

    /**
     * @inheritDoc
     */
    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        $msg = "&l&gKits Command&r";
        $msg .= "&7/kits create (Name)";
        $msg .= "&7/kits delete (Name)";
        $msg .= "&7/kits edit (Name)";

        $sender->sendMessage(TextFormat::colorize($msg));
    }

    public function getPermission(): ?string
    {
        return "kits.commands";
    }
}