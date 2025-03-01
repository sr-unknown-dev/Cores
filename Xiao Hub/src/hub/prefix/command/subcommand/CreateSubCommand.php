<?php

declare(strict_types=1);

namespace hub\prefix\command\subcommand;

use hub\prefix\command\PrefixSubCommand;
use hub\prefix\Prefix;
use hub\Loader;
use hub\player\Player;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class CreateSubCommand implements PrefixSubCommand
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

        if (count($args) < 3) {
            $sender->sendMessage(TextFormat::colorize('&cUse /prefix create [string: name] [string: format] [string: permission]'));
            return;
        }
        $prefixName = $args[0];
        $format = $args[1];
        $permission = $args[2];

        if (Loader::getInstance()->getPrefixManager()->getPrefix($prefixName) !== null) {
            $sender->sendMessage(TextFormat::colorize('&cA prefix already exists with this name'));
            return;
        }

        if (strlen($prefixName) > 10) {
            $sender->sendMessage(TextFormat::colorize('&cThe prefix name cannot contain more than 10 characters'));
            return;
        }
        $checkName = explode(' ', $prefixName);

        if (count($checkName) > 1) {
            $sender->sendMessage(TextFormat::colorize('&cThe prefix name cannot contain spaces'));
            return;
        }
        
        $checkFormat = explode(' ', $format);

        if (count($checkFormat) > 1) {
            $sender->sendMessage(TextFormat::colorize('&cThe prefix name cannot contain spaces'));
            return;
        }
        
        Loader::getInstance()->getPrefixManager()->createprefix($prefixName, [
            'format' => $format,
            'permission' => $permission
        ]);
        $sender->sendMessage(TextFormat::colorize("&aYou have created the prefix $prefixName"));
    }
}