<?php

namespace hcf\module\staffmode\commands;

use CortexPE\Commando\BaseCommand;
use hcf\arguments\PlayersArgument;
use hcf\Loader;
use hcf\player\Player;
use pocketmine\command\CommandSender;

class UnMuteCommand extends BaseCommand
{
    public function __construct()
    {
        parent::__construct(Loader::getInstance(), "unmute", "unmutear a un player");
    }

    protected function prepare(): void
    {
        $this->setPermission($this->getPermission());
        $this->registerArgument(0, new PlayersArgument("player", true));
    }

    /**
     * @inheritDoc
     */
    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        $t = $args["player"];
        if ($sender instanceof Player && $t instanceof Player)
            Loader::getInstance()->getStaffModeManager()->removeMute($sender, $t);
    }

    public function getPermission(): ?string
    {
        return "staff.cmds";
    }
}