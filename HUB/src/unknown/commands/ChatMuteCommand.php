<?php

namespace unknown\commands;

use hcf\player\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use unknown\Loader;

class ChatMuteCommand extends Command
{
    public function __construct()
    {
        parent::__construct("mchat", "Mute chat");
        $this->setPermission("admin.perms");
    }
    
    public function execute(CommandSender $player, string $label, array $args)
    {
        if (!$player instanceof Player) return;

        foreach (Server::getInstance()->getOnlinePlayers() as $players) {
            Loader::getInstance()->chatMute[$players->getName()] = true;
            Loader::getInstance()->chatMuteStatus = true;
        }
        $player->sendMessage(TextFormat::colorize("&aChat as been muted"));
    }
}