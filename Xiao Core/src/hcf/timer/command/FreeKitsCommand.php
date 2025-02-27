<?php

namespace hcf\timer\command;

use CortexPE\Commando\BaseCommand;
use hcf\Loader;
use hcf\timer\command\FreeKitsSubCommands\EnableSubCommand;
use hcf\timer\command\FreeKitsSubCommands\StopSubCommand;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat as TE;

class FreeKitsCommand extends BaseCommand {

    public function __construct()
    {
        parent::__construct(Loader::getInstance(), "freekits", "Get free kits", []);
    }

    protected function prepare(): void {
        $this->registerSubCommand(new EnableSubCommand);
        $this->registerSubCommand(new StopSubCommand);
        $this->setPermission("moderador.command");
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        $sender->sendMessage(TE::RED . "Use /freekits [on|off] [time]");
    }

    public function getPermission()
    {
        return "moderador.command";
    }
}