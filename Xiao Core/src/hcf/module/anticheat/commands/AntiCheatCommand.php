<?php

namespace hcf\module\anticheat\commands;

use CortexPE\Commando\BaseCommand;
use hcf\HCFLoader;
use hcf\Loader;
use hcf\module\anticheat\commands\subcommands\AlertsSubCommand;
use hcf\module\anticheat\commands\subcommands\ExemptSubCommand;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class AntiCheatCommand extends BaseCommand {

    public function __construct(){
        parent::__construct(
            Loader::getInstance(),
            "anticheat",
            "Toggle anti-cheat settings"
        );
    }

    protected function prepare(): void
    {
        $this->setPermission("anticheat.command");
        $this->registerSubCommand(new ExemptSubCommand("exempt", "Ya no saltan las alertas con este jugador"));
        $this->registerSubCommand(new AlertsSubCommand("alerts", "Activa y desactiva las alertas de el anti cheat"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        $sender->sendMessage(TextFormat::colorize("Use: /anticheat (alerts|exempt)"));
    }

    public function getPermission(): ?string
    {
        return "anticheat.command";
    }
}