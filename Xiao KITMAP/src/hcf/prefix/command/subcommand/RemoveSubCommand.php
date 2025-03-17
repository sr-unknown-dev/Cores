<?php

declare(strict_types=1);

namespace hcf\prefix\command\subcommand;

use hcf\prefix\command\PrefixSubCommand;
use hcf\prefix\Prefix;
use hcf\Loader;
use hcf\player\Player;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class RemoveSubCommand implements PrefixSubCommand
{

    /**
     * @param CommandSender $sender
     * @param array $args
     */
    public function execute(CommandSender $sender, array $args): void
    {
        if (!$sender->hasPermission("moderador.command")) {
            return;
        }
        if (!$sender instanceof Player)
            return;

        if (count($args) < 1) {
            $sender->sendMessage(TextFormat::colorize('&cUse /prefix remove [string: player]'));
            return;
        }
        $playerName = $args[0];
        $targetPlayer = Loader::getInstance()->getServer()->getPlayerByPrefix($playerName);
        
        if (!$targetPlayer instanceof Player) {
            $sender->sendMessage(TextFormat::RED."Jugador $playerName no encontrado!");
            return;  
        }
        
        $targetPlayer->getSession()->setPrefix(null);
        $sender->sendMessage(TextFormat::colorize("&aLe has quitar el Prefix a $playerName"));
    }
}