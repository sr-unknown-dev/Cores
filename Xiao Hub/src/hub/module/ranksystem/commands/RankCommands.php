<?php

namespace hub\module\ranksystem\commands;

use CortexPE\Commando\BaseCommand;
use hub\Loader;
use pocketmine\command\CommandSender;
use hub\module\ranksystem\commands\subcommands\CreateSubCommand;
use hub\module\ranksystem\commands\subcommands\DeleteSubCommand;
use hub\module\ranksystem\commands\subcommands\SetSubCommand;
use hub\module\ranksystem\commands\subcommands\RemoveSubCommand;
use hub\module\ranksystem\commands\subcommands\ListSubCommand;
use hub\module\ranksystem\commands\subcommands\UserInfoSubCommand;
use pocketmine\utils\TextFormat;

class RankCommands extends BaseCommand {

    public function __construct(string $name, string $description = "")
    {
        parent::__construct(Loader::getInstance(), $name, $description);
    }

    protected function prepare(): void {
        $this->setPermission($this->getPermission());

        $this->registerSubCommand(new CreateSubCommand("create", "Crear un nuevo rango"));
        $this->registerSubCommand(new DeleteSubCommand("delete", "Eliminar un rango"));
        $this->registerSubCommand(new SetSubCommand("set", "Asignar un rango a un jugador"));
        $this->registerSubCommand(new RemoveSubCommand("remove", "Remover un rango de un jugador"));
        $this->registerSubCommand(new ListSubCommand("list", "Listar todos los rangos"));
        $this->registerSubCommand(new UserInfoSubCommand("userinfo", "Mostrar información del usuario"));
    }

    public function onRun(CommandSender $sender, string $label, array $args): void {
        $sender->sendMessage(TextFormat::colorize("&l&4Ranks Command\n&r&c/ranks create\n&r&c/ranks delete\n&r&c/ranks set\n&r&c/ranks remove\n&r&c/ranks list\n&r&c/ranks userinfo"));
    }

    public function getPermission(): ?string{
        return "ranks.commands";
    }
}
