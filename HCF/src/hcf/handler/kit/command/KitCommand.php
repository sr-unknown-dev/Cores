<?php

declare(strict_types=1);

namespace hcf\handler\kit\command;

use CortexPE\Commando\BaseCommand;
use hcf\handler\kit\command\subcommand\CreateSubCommand;
use hcf\handler\kit\command\subcommand\DeleteSubCommand;
use hcf\handler\kit\command\subcommand\EditSubCommand;
use hcf\handler\kit\command\subcommand\EditKitSubCommand;
use hcf\handler\kit\command\subcommand\EditRepresentativeItemSubCommand;
use hcf\handler\kit\command\subcommand\GiveConsoleSubCommand;
use hcf\handler\kit\command\subcommand\GiveSubCommand;
use hcf\handler\kit\command\subcommand\NpcSubCommand;
use hcf\Loader;
use hcf\utils\inventorie\Inventories;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat;

/**
 * Class KitNPCCommand
 * @package hcf\handler\kit\command
 */
class KitCommand extends BaseCommand
{
    public function __construct(string $name, Translatable|string $description = "", array $aliases = [])
    {
        parent::__construct(Loader::getInstance(), $name, $description, $aliases);
    }

    public function prepare(): void
    {
        $this->setPermission($this->getPermission());
        $this->registerSubCommand(new CreateSubCommand("create", "Comando para crear un kit"));
        $this->registerSubCommand(new DeleteSubCommand("delete", "Comando para crear un kit"));
        $this->registerSubCommand(new EditSubCommand("edit", "Comando para crear un kit"));
        $this->registerSubCommand(new EditKitSubCommand("editcontent", "Comando para crear un kit"));
        $this->registerSubCommand(new NpcSubCommand("npc", "Comando para poner el npc de kits"));
        $this->registerSubCommand(new EditRepresentativeItemSubCommand("setitem", "Comando para crear un kit"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        $sender->sendMessage(TextFormat::RED."Use: /crate create (kitName) (keyFormat) (nameFormat)");
        $sender->sendMessage(TextFormat::RED."Use: /crate delete (kitName)");
        $sender->sendMessage(TextFormat::RED."Use: /crate editcontent (free|pay) (items|armor) (kitName)");
        $sender->sendMessage(TextFormat::RED."Use: /crate edit (free|pay)");
        $sender->sendMessage(TextFormat::RED."Use: /crate npc");
        $sender->sendMessage(TextFormat::RED."Use: /crate setitem");
    }

    public function getPermission()
    {
        return "kit.command";
    }
}