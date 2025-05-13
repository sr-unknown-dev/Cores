<?php

namespace hcf\handler\bounty\commands;

use CortexPE\Commando\args\FloatArgument;
use CortexPE\Commando\BaseCommand;
use hcf\arguments\PlayersArgument;
use hcf\Loader;
use hcf\player\Player;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class SetBountyCommand extends BaseCommand
{
    public function __construct()
    {
        parent::__construct(Loader::getInstance(), "setbounty", "Create bounty to player");
    }

    /**
     * @inheritDoc
     */
    protected function prepare(): void
    {
        $this->setPermission("use.player.command");
        $this->registerArgument(0, new PlayersArgument("player", true));
        $this->registerArgument(1, new FloatArgument("amount", true));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage(TextFormat::RED . "This command can only be used in-game.");
            return;
        }

        if (!$sender->hasPermission($this->getPermission())) {
            $sender->sendMessage(TextFormat::RED . "You do not have permission to use this command.");
            return;
        }

        if (!isset($args["player"]) || !isset($args["amount"])) {
            $sender->sendMessage(TextFormat::RED . "Usage: /setbounty <player> <amount>");
            return;
        }

        if ($args["player"] === $sender->getName()) {
            $sender->sendMessage(TextFormat::RED . "You cannot set a bounty on yourself.");
            return;
        }

        if ($sender->getSession()->getBalance() < $args["amount"]) {
            $sender->sendMessage(TextFormat::RED . "Your balance is insufficient.");
            return;
        }

        Loader::getInstance()->getBountyManager()->addBounty($args["player"], $sender->getName(), $args["amount"]);
        $sender->getSession()->setBalance($sender->getSession()->getBalance() - $args["amount"]);
        $sender->sendMessage(TextFormat::GREEN . "Bounty set successfully on " . $args["player"] . " for " . $args["amount"] . ".");
    }

    public function getPermission(): ?string
    {
        return "use.player.command";
    }
}