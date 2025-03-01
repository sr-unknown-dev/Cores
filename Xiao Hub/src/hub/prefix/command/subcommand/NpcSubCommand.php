<?php

declare(strict_types=1);

namespace hub\prefix\command\subcommand;

use hub\prefix\command\PrefixSubCommand;
use hub\prefix\utils\Utils;
use hub\prefix\entity\PrefixEntity;
use hub\prefix\Prefix;
use hub\Loader;
use hub\player\Player;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class NpcSubCommand implements PrefixSubCommand
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
        
        $entity = new PrefixEntity($sender->getLocation(), $sender->getSkin(), Utils::createBasicNBT($sender));
        $entity->spawnToAll();
        $sender->sendMessage(TextFormat::colorize("&aHas colocado el Prefix Entity"));
        return;
    }
}
