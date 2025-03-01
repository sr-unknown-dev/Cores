<?php

namespace hcf\command\fix;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseCommand;
use hcf\command\fix\subcommand\AllSubCommand;
use hcf\command\fix\subcommand\AutoSubCommand;
use hcf\command\fix\subcommand\HandSubCommand;
use hcf\command\fix\subcommand\HelpSubCommand;
use hcf\Loader;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class FixCommand extends BaseCommand
{
    public function __construct(string $name, string $description = "")
    {
        parent::__construct(
            Loader::getInstance(),
            $name,
            $description
        );
    }

    protected function prepare(): void
    {
        $this->setPermission($this->getPermission());
        $this->registerSubCommand(new AllSubCommand("all", "te repara todo el inventario"));
        $this->registerSubCommand(new HandSubCommand("hand", "para repararle el inventario a otro jugador"));
        $this->registerSubCommand(new AutoSubCommand("auto", "te repare automaticamente el inventario"));
        $this->registerSubCommand(new HelpSubCommand("help", "te dice los comandos existentes"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        $sender->sendMessage(TextFormat::YELLOW."Use: /fix help");
    }

    public function getPermission()
    {
        return "fix.player.command";
    }
}