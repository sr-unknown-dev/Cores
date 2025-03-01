<?php

namespace hcf\timer\command\subcommands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use hcf\Loader;
use hcf\timer\types\TimerAirdrop;
use hcf\timer\types\TimerKey;
use hcf\timer\types\TimerKeyOP;
use hcf\timer\types\TimerPackages;
use hcf\utils\time\Timer;
use pocketmine\command\CommandSender;
use pocketmine\item\Armor;
use pocketmine\item\Durable;
use pocketmine\utils\TextFormat;

class EnableSubCommand extends BaseSubCommand
{
    public function __construct(string $name, string $description = "")
    {
        parent::__construct($name, $description);
    }

    protected function prepare(): void
    {
        $this->setPermission($this->getPermission());
        $this->registerArgument(0, new RawStringArgument("time"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (Loader::getInstance()->getTimerManager()->getSotw()->isActive()) {
            $sender->sendMessage(TextFormat::colorize('&cThe sotw is already started'));
            return;
        }
        $time = $args["time"];

        $time = Timer::time($time);
        Loader::getInstance()->getTimerManager()->getSotw()->setActive(true);
        Loader::getInstance()->getTimerManager()->getSotw()->setTime((int) $time);
        TimerKey::start(900);   // 15 minutos
        TimerKeyOP::start(1200);   // 20 minutos
        TimerPackages::start(1500);   // 25 minutos
        TimerAirdrop::start(1800);   // 30 minutos
        $sender->sendMessage(TextFormat::colorize('&aThe sotw has started!'));
    }

    public function getPermission(): ?string
    {
        return "sotw.command";
    }
}