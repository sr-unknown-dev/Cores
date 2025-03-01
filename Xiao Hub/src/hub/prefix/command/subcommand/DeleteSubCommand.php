<?php

declare(strict_types=1);

namespace hub\prefix\command\subcommand;

use hub\prefix\command\PrefixSubCommand;
use hub\prefix\Prefix;
use hub\Loader;
use hub\player\Player;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class DeleteSubCommand implements PrefixSubCommand
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
        
        $prefixName = $args[0];
        
        if (Loader::getInstance()->getPrefixManager()->getPrefix($prefixName) === null) {
            $sender->sendMessage(TextFormat::colorize("&cThe prefix $prefixName non exists"));
            return;
        }
        
        Loader::getInstance()->getPrefixManager()->removePrefix($prefixName);
        $sender->sendMessage(TextFormat::colorize('&cThe prefix has been deleted'));
    }
}
