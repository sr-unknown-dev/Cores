<?php

namespace hcf\handler\crate\command\subcommand;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\BaseSubCommand;
use hcf\faction\Faction;
use hcf\Loader;
use hcf\player\Player;
use pocketmine\command\CommandSender;
use pocketmine\item\VanillaItems;
use pocketmine\utils\TextFormat;

class ItemConfigurationSubCommand extends BaseSubCommand
{
    public function __construct(string $name, string $description = "", array $aliases = [])
    {
        parent::__construct($name, $description, $aliases);
    }

    protected function prepare(): void
    {
        $this->setPermission($this->getPermission());
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender instanceof Player)
            return;
        $item = VanillaItems::GOLDEN_AXE();
        $item->setCustomName(TextFormat::colorize('&4Crate Configuration'));
        $item->setNamedTag($item->getNamedTag()->setString('crate_configuration', 'true'));
        
        $sender->getInventory()->addItem($item);
    }

    public function getPermission(): ?string
    {
        return "crate.command.itemconfig";
    }
}