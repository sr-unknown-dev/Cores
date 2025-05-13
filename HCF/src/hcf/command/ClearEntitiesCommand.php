<?php

declare(strict_types=1);

namespace hcf\command;

use hcf\entity\TextEntity;
use hcf\player\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class ClearEntitiesCommand extends Command
{

    public function __construct()
    {
        parent::__construct('clearentities', 'Usa este comando para eliminar entidades');
        $this->setPermission('clearentities.command');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$sender instanceof Player) return;
        if (!$this->testPermission($sender)) {
            $sender->sendMessage(TextFormat::colorize('&cNo tienes permisos'));
            return;
        }
        $count = 0;

        foreach ($sender->getWorld()->getEntities() as $entity) {
            if ($entity instanceof TextEntity) {
                $entity->flagForDespawn();
                $count++;
            }
        }
        $sender->sendMessage('§4Entidades de las crates eliminadas§b: ' . $count);
    }
}