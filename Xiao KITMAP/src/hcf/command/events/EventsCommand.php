<?php

namespace hcf\command\events;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseCommand;
use hcf\command\events\subcommands\HcfSubCommand;
use hcf\command\events\subcommands\MapSubCommand;
use hcf\command\fix\subcommands\AllSubCommand;
use hcf\command\pay\args\PlayersOnline;
use hcf\command\pay\subcommands\PaySubCommand;
use hcf\Loader;
use hcf\player\Player;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class EventsCommand extends BaseCommand
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
        $this->registerSubCommand(new HcfSubCommand("hcf", "abre el menu de los eventos como keyall,airdropall etc.."));
        $this->registerSubCommand(new MapSubCommand("map", "abre el menu de los eventos como sotw,purga etc..."));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        $sender->sendMessage(TextFormat::YELLOW."Use: /events (hcf | map) (time)");
    }

    public function getPermission()
    {
        return "moderador.command";
    }
}