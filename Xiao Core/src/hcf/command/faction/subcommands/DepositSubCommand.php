<?php

namespace hcf\command\faction\subcommands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\BaseSubCommand;
use hcf\faction\Faction;
use hcf\Loader;
use hcf\player\Player;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class DepositSubCommand extends BaseSubCommand
{
    public function __construct(string $name, string $description = "")
    {
        parent::__construct($name, $description);
    }

    protected function prepare(): void
    {
        $this->setPermission($this->getPermission());
        $this->registerArgument(0, new RawStringArgument("amount", true));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender instanceof Player)
            return;

        if ($sender->getSession()->getFaction() === null) {
            $sender->sendMessage(TextFormat::colorize('&cYou don\'t have a faction'));
            return;
        }

        if ($args["amount"] === null) {
            $sender->sendMessage(TextFormat::colorize('&cYou need to specify an amount'));
            return;
        }

        $faction = Loader::getInstance()->getFactionManager()->getFaction($sender->getSession()->getFaction());
        $amount = $args["amount"];

        if ($amount === "all") {
            $amount = $sender->getSession()->getBalance();
            $faction->setBalance($faction->getBalance() + $amount);
            $sender->getSession()->setBalance(0);
        } else {
            $amount = (int)$amount;
            if ($amount <= 0 || $amount > $sender->getSession()->getBalance()) {
                $sender->sendMessage(TextFormat::colorize('&cInvalid amount'));
                return;
            }
        }

        $faction->setBalance($faction->getBalance() + $amount);
        $sender->sendMessage('§aThe new balance of the faction is ' . $faction->getBalance() . '$');
        $sender->getSession()->setBalance($sender->getSession()->getBalance() - $amount);
    }

    public function getPermission(): ?string
    {
        return "use.player.command";
    }
}