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
        parent::__construct(Loader::getInstance(), "setbounty", "Crete bounty to player");
    }

    /**
     * @inheritDoc
     */
    protected function prepare(): void
    {
        $this->setPermission($this->getPermission());
        $this->registerArgument(0, new PlayersArgument("player", true));
        $this->registerArgument(1, new FloatArgument("amount", true));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if ($sender instanceof Player){
            if ($sender->getSession()->getBalance() >= $args["amount"]){
                Loader::getInstance()->getBountyManager()->addBounty($args["player"], $sender->getName(), $args["amount"]);
                $sender->getSession()->setBalance($sender->getSession()->getBalance() - $args["amount"]);
            }else{
                $sender->sendMessage(TextFormat::RED."Your balance is insufficient");
            }
        }else{
            return;
        }
    }

    public function getPermission(): ?string
    {
        return "use.player.command";
    }
}