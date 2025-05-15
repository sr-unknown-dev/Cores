<?php

namespace unknown\commands;

use hcf\player\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use unknown\Loader;

class UnMuteChatCommand extends Command
{
    public function __construct()
    {
        parent::__construct("umchat", "Un Mute chat");
    }

    public function execute(CommandSender $player, string $label, array $args)
    {
        if (!$player instanceof Player) return;

        foreach (Server::getInstance()->getOnlinePlayers() as $players) {
            unset(Loader::getInstance()->chatMute[$players->getName()]);
            Loader::getInstance()->chatMuteStatus = false;
        }
        $player->sendMessage(TextFormat::colorize("&aChat as been muted"));
    }
}