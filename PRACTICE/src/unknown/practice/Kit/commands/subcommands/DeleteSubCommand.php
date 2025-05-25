<?php

namespace unknown\practice\Kit\commands\subcommands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use unknown\practice\Kit\Kit;
use unknown\practice\Kit\KitManager;

class DeleteSubCommand extends BaseSubCommand
{
    public function __construct()
    {
        parent::__construct('create', 'delete kit a subcommand.');
    }

    protected function prepare(): void
    {
        $this->setPermission('kits.commands');
        $this->registerArgument(0, new RawStringArgument("kitName", false));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage("§cEste comando solo puede usarse en el juego.");
            return;
        }

        $kitName = $args["kitName"];

        $kitManager = KitManager::getInstance();

        if ($kitManager->getKit($kitName) === null) {
            $sender->sendMessage("§cEl kit §e$kitName §cno existe.");
            return;
        }

        $kitManager->removeKit($kitName);

        $sender->sendMessage("§aKit §e$kitName §aremovido con éxito.");
    }
}