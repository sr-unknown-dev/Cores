<?php

namespace hcf\timer\command\subcommands;

use CortexPE\Commando\BaseSubCommand;
use hcf\Loader;
use hcf\timer\types\TimerAirdrop;
use hcf\timer\types\TimerKey;
use hcf\timer\types\TimerKeyOP;
use hcf\timer\types\TimerPackages;
use hcf\utils\time\Timer;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class StopSubCommand extends BaseSubCommand
{
    public function __construct(string $name, string $description = "")
    {
        parent::__construct($name, $description);
    }

    protected function prepare(): void
    {
        $this->setPermission($this->getPermission());
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!Loader::getInstance()->getTimerManager()->getSotw()->isActive()) {
            $sender->sendMessage(TextFormat::colorize('&cThe sotw has not started'));
            return;
        }
        Loader::getInstance()->getTimerManager()->getSotw()->setActive(false);
        $sender->sendMessage(TextFormat::colorize('&cYou have turned off the sotw'));
    }

    public function getPermission(): ?string
    {
        return "sotw.command";
    }
}