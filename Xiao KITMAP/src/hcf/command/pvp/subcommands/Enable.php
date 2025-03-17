<?php

namespace hcf\command\pvp\subcommands;

use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\BaseSubCommand;
use hcf\Loader;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class Enable extends BaseSubCommand
{
    public function __construct(string $name, string $description = "", array $aliases = [])
    {
        parent::__construct($name, $description, $aliases);
    }

    protected function prepare(): void
    {
        $this->setPermission($this->getPermission());
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if ($sender->getSession()->getCooldown('starting.timer') === null && $sender->getSession()->getCooldown('pvp.timer') === null) {
            $sender->sendMessage(TextFormat::colorize('&cYou don\'t have starting timer or pvp timer'));
            return;
        }
        
        if ($sender->getSession()->getCooldown('starting.timer') !== null)
            $sender->getSession()->removeCooldown('starting.timer');
        
        if ($sender->getSession()->getCooldown('pvp.timer') !== null)
            $sender->getSession()->removeCooldown('pvp.timer');
        $sender->getSession()->addCooldown('sotw.pvp', '', 3600, false, false);
        if (Loader::getInstance()->getTimerManager()->getSotw()->isActive()) {
            $sender->sendMessage(TextFormat::colorize('&aYou succesfully enabled your pvp in sotw'));
            return;
        }
        $sender->sendMessage(TextFormat::colorize('&aYou successfully enabled your pvp'));
    }

    public function getPermission(): ?string
    {
        return "use.player.command";
    }
}