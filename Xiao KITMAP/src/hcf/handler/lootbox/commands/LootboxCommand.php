<?php

namespace hcf\handler\lootbox\commands;

use CortexPE\Commando\BaseCommand;
use hcf\handler\lootbox\commands\subcommands\EditSubCommand;
use hcf\handler\lootbox\commands\subcommands\GiveSubCommand;
use hcf\Loader;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat;

class LootboxCommand extends BaseCommand
{
    public function __construct(string $name, string $description = "")
    {
        parent::__construct(Loader::getInstance(), $name, $description);
    }

    protected function prepare(): void
    {
        $this->setPermission($this->getPermission());
        $this->registerSubCommand(new EditSubCommand("edit", "Lootbox edit content"));
        $this->registerSubCommand(new GiveSubCommand("give", "Lootbox give"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        $sender->sendMessage(TextFormat::GREEN."Use /lootbox [edit|give]"));
    }

    public function getPermission(): ?string
    {
        return "lootbox.command";
    }
}