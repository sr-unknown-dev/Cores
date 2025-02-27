<?php

namespace hcf\command\moderador;

use hcf\entity\server\BountyEntity;
use hcf\player\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class BountyCommand extends Command
{
    public function __construct()
    {
        parent::__construct("bountyn", "Pone un npc de el bounty", "/bountyn");
        $this->setPermission("moderador.command");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender->isPermissionSet("moderador.command")){
            return;
        }
        
        if ($sender instanceof Player) {
            $entity = BountyEntity::create($sender);
            $entity->spawnToAll();
            $sender->sendMessage(TextFormat::GREEN."El npc se puso exitosamente");
            $sender->sendMessage(TextFormat::GREEN."El npc se puso exitosamente");
        }
    }
}
