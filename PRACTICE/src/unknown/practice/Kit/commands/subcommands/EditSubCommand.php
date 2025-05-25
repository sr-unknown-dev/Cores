<?php

namespace unknown\practice\Kit\commands\subcommands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use hcf\player\Player;
use pocketmine\command\CommandSender;
use unknown\practice\Kit\KitManager;

class EditSubCommand extends BaseSubCommand
{
    public function __construct()
    {
        parent::__construct('edit', 'edit kit a subcommand.');
    }

    protected function prepare(): void
    {
        $this->setPermission('kits.commands');
        $this->registerArgument(0, new RawStringArgument("kitName", false));
        $this->registerArgument(1, new RawStringArgument("armor|items", false)); // armor o items
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage("§cEste comando solo puede usarse en el juego.");
            return;
        }

        $kitName = $args["kitName"];
        $editType = strtolower($args["editType"]);

        $kitManager = KitManager::getInstance();
        $kit = $kitManager->getKit($kitName);

        if ($kit === null) {
            $sender->sendMessage("§cEl kit §e$kitName §cno existe.");
            return;
        }

        switch (strtolower($editType)) {
            case 'armor':
                $this->editArmor($sender, $kit, $kitManager, $kitName);
                break;
            case 'items':
                $this->editItems($sender, $kit, $kitManager, $kitName);
                break;
            default:
                $sender->sendMessage("§cTipo inválido. Usa 'items' o 'armor'.");
                break;
        }
    }

    public function editItems($sender, $kit, $kitManager, $kitName)
    {
        $kit->setItems($sender->getInventory()->getContents());
        $kitManager->createKit($kit);
        $sender->sendMessage("§aHas editado los §eitems §adel kit §b$kitName.");
    }

    public function editArmor($sender, $kit, $kitManager, $kitName)
    {
        $kit->setArmor($sender->getArmorInventory()->getContents());
        $kitManager->createKit($kit);
        $sender->sendMessage("§aHas editado la §earmadura §adel kit §b$kitName.");
    }
}