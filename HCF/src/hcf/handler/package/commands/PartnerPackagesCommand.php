<?php

namespace hcf\handler\package\commands;

use CortexPE\Commando\BaseCommand;
use hcf\handler\package\commands\subcommands\EditContentSubCommand;
use hcf\handler\package\commands\subcommands\GiveSubCommand;
use hcf\Loader;
use hcf\player\Player;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class PartnerPackagesCommand extends BaseCommand
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
        $this->registerSubCommand(new EditContentSubCommand("editcontent", "Para editar el contenido de los Parter Package"));
        $this->registerSubCommand(new GiveSubCommand("give", "Para givearte Parter Packages"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if ($sender instanceof Player) {
            $sender->sendMessage(TextFormat::RED."Use: /pkg (editcontent|give)");
        }
    }

    public function getPermission()
    {
        return 'package.command';
    }
}