<?php

namespace hcf\handler\crate\command;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseCommand;
use hcf\command\fix\subcommand\AllSubCommand;
use hcf\command\fix\subcommand\HandSubCommand;
use hcf\command\fix\subcommand\HelpSubCommand;
use hcf\handler\crate\command\subcommand\CreateSubCommand;
use hcf\handler\crate\command\subcommand\DeleteSubCommand;
use hcf\handler\crate\command\subcommand\EditSubCommand;
use hcf\handler\crate\command\subcommand\GiveKeySubCommand;
use hcf\handler\crate\command\subcommand\GiveSubCommand;
use hcf\handler\crate\command\subcommand\ItemConfigurationSubCommand;
use hcf\Loader;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class CrateCommand extends BaseCommand
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
        $this->registerSubCommand(new CreateSubCommand("create", "para crear una crate"));
        $this->registerSubCommand(new DeleteSubCommand("delete", "para eliminar una crate"));
        $this->registerSubCommand(new EditSubCommand("edit", "para editar una crate"));
        $this->registerSubCommand(new GiveKeySubCommand("givekey", "para darte una key"));
        $this->registerSubCommand(new GiveSubCommand("give", "te da la crate"));
        $this->registerSubCommand(new ItemConfigurationSubCommand("itemconfig", "para el item de una crate"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        $sender->sendMessage(TextFormat::RED."Use: /crate create (crateName) (keyFormat) (nameFormat)");
        $sender->sendMessage(TextFormat::RED."Use: /crate delete (crateName)");
        $sender->sendMessage(TextFormat::RED."Use: /crate edit (name)");
        $sender->sendMessage(TextFormat::RED."Use: /crate givekey (crateName) (player) (amount)");
        $sender->sendMessage(TextFormat::RED."Use: /crate give (crateName)");
        $sender->sendMessage(TextFormat::RED."Use: /crate itemconfig");
    }

    public function getPermission()
    {
        return "crate.command";
    }
}