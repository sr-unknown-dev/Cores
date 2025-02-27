<?php

namespace hcf\module\anticheat\commands\subcommands;

use CortexPE\Commando\BaseSubCommand;
use hcf\HCFLoader;
use hcf\Loader;
use hcf\player\Player;
use pocketmine\command\CommandSender;

class AlertsSubCommand extends BaseSubCommand
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

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {

        if ($sender instanceof Player)
        Loader::getInstance()->getAntiCheatManager()->toggleAlerts($sender);
    }

    public function getPermission(): ?string
    {
        return "anticheat.command";
    }
}