<?php

namespace hcf\command\pvp;

use CortexPE\Commando\BaseCommand;
use hcf\command\pvp\subcommands\Enable;
use hcf\Loader;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class PvPCommand extends BaseCommand
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
        $this->registerSubCommand(new Enable("enable", "desactivar el pvp timer", ["on"]));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        $sender->sendMessage(TextFormat::YELLOW."Use: /pvp enable");
    }

    public function getPermission()
    {
        return "use.player.command";
    }
}