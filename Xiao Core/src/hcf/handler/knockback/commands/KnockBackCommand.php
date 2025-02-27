<?php

namespace hcf\handler\knockback\commands;

use CortexPE\Commando\BaseCommand;
use pocketmine\command\CommandSender;
use hcf\handler\knockback\commands\subcommands\SetHorizontalSubCommand;
use hcf\handler\knockback\commands\subcommands\SetVerticalSubCommand;
use hcf\handler\knockback\commands\subcommands\SetDelaySubCommand;
use hcf\Loader;

class KnockBackCommand extends BaseCommand {

    public function __construct(string $name, string $description = "", array $aliases = []) {
        parent::__construct(Loader::getInstance(), $name, $description, $aliases);
    }

    public function prepare(): void {
        $this->setPermission("knockback.command");
        $this->registerSubCommand(new SetHorizontalSubCommand("horizontal", "Establece el knockback horizontal"));
        $this->registerSubCommand(new SetVerticalSubCommand("vertical", "Establece el knockback vertical"));
        $this->registerSubCommand(new SetDelaySubCommand("delay", "Establece el retraso del knockback"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
        $sender->sendMessage("Uso: /knockback <horizontal|vertical|delay> <valor>");
    }

    public function getPermission(): string {
        return "knockback.command";
    }
}
