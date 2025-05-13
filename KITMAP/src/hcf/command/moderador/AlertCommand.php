<?php

declare(strict_types=1);

namespace hcf\command\moderador;

use hcf\player\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Server;
use pocketmine\utils\TextFormat as TE;

class AlertCommand extends Command
{

    public function __construct()
    {
        parent::__construct('alert', 'Send an Alert to everyone!');
        $this->setPermission("moderador.command");
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$sender instanceof Player) return;
        
        if (!$this->testPermission($sender))
            return;
           
if (count($args) === 0) {
            $sender->sendMessage(TE::RED . "/alert (string | text)");
            return;
        }
        $msg = implode(" ", $args);
if (count($args)) {
            
Server::getInstance()->broadcastMessage(TE::colorize("§f[§c!§f]§r§c".$msg));
Server::getInstance()->broadcastMessage(TE::colorize("§f[§c!§f]§r§c".$msg));
Server::getInstance()->broadcastMessage(TE::colorize("§f[§c!§f]§r§c".$msg));
Server::getInstance()->broadcastMessage(TE::colorize("§f[§c!§f]§r§c".$msg));
Server::getInstance()->broadcastMessage(TE::colorize("§f[§c!§f]§r§c".$msg));
Server::getInstance()->broadcastMessage(TE::colorize("§f[§c!§f]§r§c".$msg));
return;
}
    }
}