<?php

namespace hcf\command;

use hcf\Loader;
use hcf\player\Player;
use MongoDB\Driver\Server;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class PingCommand extends Command
{
    public function __construct()
    {
        parent::__construct("ping", TextFormat::YELLOW."Muestra tu ping", "/ping");
        $this->setPermission('use.player.command');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$sender instanceof Player){
            $sender->sendMessage(TextFormat::RED."Este comando solo puede ser utilizado en el juego");
        }

        if ($sender instanceof Player){
            $ping = $sender->getNetworkSession()->getPing();
            $sender->sendMessage(TextFormat::colorize("&aYour ping is : &f".$ping));
        }
    }
}