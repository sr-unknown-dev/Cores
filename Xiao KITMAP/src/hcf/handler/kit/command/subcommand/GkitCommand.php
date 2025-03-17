<?php

namespace hcf\handler\kit\command\subcommand;

use CortexPE\Commando\BaseCommand;
use hcf\Loader;
use hcf\player\Player;
use hcf\utils\inventorie\Inventories;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\plugin\Plugin;

class GkitCommand extends BaseCommand
{
    public function __construct(string $name, Translatable|string $description = "", array $aliases = [])
    {
        parent::__construct(Loader::getInstance(), $name, $description, $aliases);
    }

    /**
     * @inheritDoc
     */
    protected function prepare(): void
    {
        $this->setPermission($this->getPermission());
    }

    /**
     * @inheritDoc
     */
    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if ($sender instanceof Player){
            Inventories::createMenuKit($sender);
        }
    }

    public function getPermission()
    {
        return "use.player.command";
    }
}