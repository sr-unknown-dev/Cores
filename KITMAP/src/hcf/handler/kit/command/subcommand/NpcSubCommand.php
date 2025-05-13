<?php

namespace hcf\handler\kit\command\subcommand;

use CortexPE\Commando\BaseSubCommand;
use hcf\entity\server\GkitEntity;
use hcf\player\Player;
use pocketmine\command\CommandSender;

class NpcSubCommand extends BaseSubCommand
{
    public function __construct(string $name, string $description = "", array $aliases = [])
    {
        parent::__construct($name, $description, $aliases);
    }

    /**
     * @inheritDoc
     */
    protected function prepare(): void
    {
        $this->setPermission($this->getPermission());
    }

    /**
     * @param CommandSender $sender
     * @param string $aliasUsed
     * @param array $args
     * @return void
     */
    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender instanceof Player)
            return;

        if ($sender instanceof Player) {
            $entity = GkitEntity::create($sender);
            $entity->spawnToAll();
            return;
        }
    }
    
    public function getPermission(): ?string
    {
        return 'kit.command';
    }
}