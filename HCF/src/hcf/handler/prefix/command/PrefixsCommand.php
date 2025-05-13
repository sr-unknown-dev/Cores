<?php

namespace hcf\handler\prefix\command;

use hcf\handler\prefix\menu\PrefixMenu;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class PrefixsCommand extends Command
{

    public function __construct()
    {
        parent::__construct("prefixs", "Open Menu Prefixs");
        $this->setPermission("prefix.user");
    }

    /**
     * @inheritDoc
     */
    public function execute(CommandSender $player, string $label, array $args)
    {
        if (!$player instanceof Player) return;

        if ($player instanceof Player) {
            PrefixMenu::open($player);
        }
    }
}