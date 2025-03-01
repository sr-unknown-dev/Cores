<?php

namespace hcf\command\moderador;

use hcf\Factory;
use hcf\player\Player;
use hcf\StaffMode\Freeze;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\inventory\PlayerCraftingInventory;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class PruebaCommand extends Command
{
    public function __construct()
    {
        parent::__construct("craft", "Te dice la informacion del mapa", "/craft");
        $this->setPermission("use.player.command");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof Player) {
            $x = $sender->getPosition()->getFloorX();
        $y = $sender->getPosition()->getFloorY() + 5;
        $z = $sender->getPosition()->getFloorZ();
        $pos = new Position($x, $y, $z, $sender->getWorld());
        }
    }
}
