<?php

namespace hcf\handler\lootbox\commands\subcommands;

use CortexPE\Commando\args\FloatArgument;
use CortexPE\Commando\BaseSubCommand;
use hcf\Loader;
use hcf\player\Player;
use pocketmine\command\CommandSender;

class GiveSubCommand extends BaseSubCommand
{
    public function __construct(string $name, string $description = "")
    {
        parent::__construct($name, $description);
    }

    /**
     * @inheritDoc
     */
    protected function prepare(): void
    {
        $this->setPermission($this->getPermission());
        $this->registerArgument(0, new FloatArgument("amount", false));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender instanceof Player) return;

        Loader::getInstance()->getLootboxManager()->getLootbox()->giveLootbox($sender, $args["amount"]);
    }

    public function getPermission(): ?string
    {
        return "lootbox.give";
    }
}