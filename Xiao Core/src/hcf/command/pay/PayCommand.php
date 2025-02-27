<?php

namespace hcf\command\pay;

use CortexPE\Commando\args\IntegerArgument;
use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseCommand;
use hcf\arguments\PlayersArgument;
use hcf\command\fix\subcommands\AllSubCommand;
use hcf\command\pay\args\PlayersOnline;
use hcf\command\pay\subcommands\PaySubCommand;
use hcf\Loader;
use hcf\player\Player;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class PayCommand extends BaseCommand
{
    public function __construct(string $name, string $description = "")
    {
        parent::__construct(
            Loader::getInstance(),
            $name,
            $description
        );
    }

    protected function prepare(): void
    {
        $this->setPermission($this->getPermission());
        $this->registerArgument(0, new PlayersArgument("player"), true);
        $this->registerArgument(1, new RawStringArgument("amount"), true);
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender instanceof Player) return;
        
        if (!isset($args["player"]) || !isset($args["amount"])) {
            $sender->sendMessage(TextFormat::colorize('&cUse /pay [player] [money]'));
            return;
        }
        $player = $args["player"];
        
        if (!$player instanceof Player) {
            $sender->sendMessage(TextFormat::colorize('&cPlayer not found'));
            return;
        }
        
        if (!is_numeric($args["amount"])) {
            $sender->sendMessage(TextFormat::colorize('&cWrite an amount in numbers'));
            return;
        }
        $money = intval($args["amount"]);

        if ($money < 0) {
            return;
        }

        if ($sender->getSession()->getBalance() < $money){
            $sender->sendMessage(TextFormat::colorize("&cYou don\'t have enough money"));
            return;
        }

        
        if ($sender->getSession()->getBalance() === 0) {
            $sender->sendMessage(TextFormat::colorize('&cYou have no money'));
            return;
        }
        $result = $sender->getSession()->getBalance() - $money;
        
        if ($result > 0) {
            $sender->sendMessage(TextFormat::colorize("&cYou don\'t have enough money"));
            return;
        }
        $player->getSession()->setBalance($player->getSession()->getBalance() + $money);
        $sender->getSession()->setBalance($result);
        
        $player->sendMessage(TextFormat::colorize('&aYou received $' . $money . ' from ' . $sender->getName()));
        $sender->sendMessage(TextFormat::colorize('&aYou have sent $' . $money . ' to ' . $player->getName()));
    }

    public function getPermission()
    {
        return "use.player.command";
    }
}