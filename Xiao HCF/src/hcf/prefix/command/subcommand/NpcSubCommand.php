<?php

declare(strict_types=1);

namespace hcf\prefix\command\subcommand;

use hcf\prefix\command\PrefixSubCommand;
use hcf\prefix\utils\Utils;
use hcf\prefix\entity\PrefixEntity;
use hcf\prefix\Prefix;
use hcf\Loader;
use hcf\player\Player;
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
