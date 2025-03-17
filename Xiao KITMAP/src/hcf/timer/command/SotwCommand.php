<?php

namespace hcf\timer\command;

use CortexPE\Commando\BaseCommand;
use hcf\Loader;
use hcf\timer\command\subcommands\EnableSubCommand;
use hcf\timer\command\subcommands\StopSubCommand;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat;

class SotwCommand extends BaseCommand
{
    public function __construct(string $name, Translatable|string $description = "")
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
        $this->registerSubCommand(new EnableSubCommand("on", "Para encender el sotw"));
        $this->registerSubCommand(new StopSubCommand("off", "Para apagar el sotw"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        $sender->sendMessage(TextFormat::YELLOW."Usage: /sotw (on|off");
    }

    public function getPermission(): ?string
    {
        return "sotw.command";
    }
}