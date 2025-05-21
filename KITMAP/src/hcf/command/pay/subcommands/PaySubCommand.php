<?php

namespace hcf\command\pay\subcommands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use hcf\arguments\PlayersArgument;
use hcf\command\pay\args\AmountArgument;
use hcf\command\pay\args\PlayersOnline;
use hcf\player\Player;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class PaySubCommand extends BaseSubCommand
{

    public function __construct(string $name, string $description = "", array $aliases = [])
    {
        parent::__construct($name, $description, $aliases);
    }

    protected function prepare(): void
    {
        $this->setPermission($this->getPermission());
        $this->registerArgument(0, new RawStringArgument("amount"), true);
        $this->registerArgument(1, new PlayersArgument("player"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
    	if (!$sender instanceof Player) return;
        
        if (!isset($args[0]) || !isset($args[1])) {
            $sender->sendMessage(TextFormat::colorize('&cUse /pay [player] [money]'));
            return;
        }
        $player = $sender->getserver()->getPlayerByPrefix($args["player"]);
        
        if (!$player instanceof Player) {
            $sender->sendMessage(TextFormat::colorize('&cPlayer not found'));
            return;
        }
        
        if (!is_numeric($args[1])) {
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

    public function getPermission(): ?string
    {
        return "use.player.command";
    }
}