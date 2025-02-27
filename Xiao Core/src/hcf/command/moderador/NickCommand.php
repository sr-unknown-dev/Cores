<?php

namespace hcf\command\moderador;

use hcf\faction\Faction;
use hcf\Factory;
use hcf\Loader;
use hcf\player\Player;
use hcf\Server\Nick;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class NickCommand extends Command
{
    public function __construct()
    {
        parent::__construct("nick", "Muestra tus kils", "/nick (name)");
        $this->setPermission("moderador.command");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
            $name = implode(" ", $args);

            if ($name) {
                Factory::setNick($sender, $name);
                $sender->sendMessage(TextFormat::GREEN."Tu nombre se a cambiado a: ".TextFormat::AQUA.$name);
            }
    }
}