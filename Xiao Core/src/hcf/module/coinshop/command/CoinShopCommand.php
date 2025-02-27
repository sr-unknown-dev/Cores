<?php

declare(strict_types=1);

namespace hcf\module\coinshop\command;

use hcf\module\coinshop\entity\CoinShopEntity;
use hcf\module\coinshop\utils\Utils;
use hcf\player\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class CoinShopCommand extends Command
{

    public function __construct()
    {
        parent::__construct('coinshop', 'Command for coinshop');
        $this->setPermission('coinshop.command');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$sender instanceof Player) {
            return;
        }

        if ($sender->getCurrentClaim() !== 'Spawn') {
            return;
        }

        if (count($args) < 0) {
            Utils::openCoinShop($sender);
        }

        if (isset($args[0]) && $sender->getServer()->isOp($sender->getName())) {
            if ($args[0] === 'npc') {
                $entity = new CoinShopEntity($sender->getLocation(), $sender->getSkin());
                $entity->spawnToAll();
                $sender->sendMessage("CoinShop NPC spawned.");
                return;
            }
        }
        $sender->sendMessage("Usage: /coinshop npc");
    }
}
