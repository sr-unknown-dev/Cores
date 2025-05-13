<?php

namespace hcf\command;

use hcf\player\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class NickCommand extends Command {

    public function __construct() {
        parent::__construct("nick", "nick command");
    }

    public function execute(CommandSender $player, string $label, array $args)
    {
        if (!$player instanceof Player) {
            $player->sendMessage(TextFormat::RED . "This command can only be used in-game.");
            return;
        }

        if (count($args) < 1) {
            $player->sendMessage(TextFormat::RED . "Usage: /nick <name>");
            return;
        }

        $newName = implode(" ", $args);
        if (strlen($newName) > 16) {
            $player->sendMessage(TextFormat::RED . "The nickname is too long. Maximum length is 16 characters.");
            return;
        }

        $player->setDisplayName($newName);
        $player->setNameTag($newName);
        $player->sendMessage(TextFormat::GREEN . "Your nickname has been changed to " . TextFormat::AQUA . $newName);
    }
}