<?php

namespace hcf\handler\bounty\commands;

use hcf\player\Player;
use hcf\utils\inventorie\Inventories;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;

class BountyCommand extends Command
{
    public function __construct()
    {
        parent::__construct("bounty", "Open Bounty Menu");
        $this->setPermission("use.player.command");
    }

    /**
     * @inheritDoc
     */
    public function execute(CommandSender $player, string $label, array $args)
    {
        if ($player instanceof Player){
            Inventories::BountyMenu($player);
        }else{
            return;
        }
    }
}