<?php

declare(strict_types=1);

namespace hub\prefix\command\subcommand;

use hub\prefix\command\PrefixSubCommand;
use hub\prefix\Prefix;
use hub\Loader;
use hub\player\Player;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class SetSubCommand implements PrefixSubCommand
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

        if (count($args) < 2) {
            $sender->sendMessage(TextFormat::colorize('&cUse /prefix set [string: player] [string: prefix]'));
            return;
        }
        $playerName = $args[0];
        $targetPlayer = Loader::getInstance()->getServer()->getPlayerExact($playerName);
        $prefixName = $args[1];
        
        if (!$targetPlayer instanceof Player) {
            $sender->sendMessage(TextFormat::RED."Jugador $targetPlayer no encontrado!");
            return;  
        }
        
        if (Loader::getInstance()->getPrefixManager()->getPrefix($prefixName) === null) {
            $sender->sendMessage(TextFormat::colorize("&cPrefix $prefixName no existe"));
            return;
        }


        if (Loader::getInstance()->getPrefixManager()->getPrefix($prefixName) !== null) {
            $targetPlayer->getSession()->setPrefix($prefixName);
            $sender->sendMessage(TextFormat::colorize("&aLe has dado el Prefix $prefixName a $playerName"));
        }
    }
}