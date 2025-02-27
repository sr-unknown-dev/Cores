<?php

namespace hcf\command\faction\subcommands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\BaseSubCommand;
use hcf\faction\Faction;
use hcf\Loader;
use hcf\player\Player;
use hcf\Server\ClaimSe;
use pocketmine\command\CommandSender;
use pocketmine\item\VanillaItems;
use pocketmine\utils\TextFormat;

class WithdrawSubCommand extends BaseSubCommand
{

    public function __construct(string $name, string $description = "", array $aliases = [])
    {
        parent::__construct($name, $description, $aliases);
    }

    protected function prepare(): void
    {
        $this->setPermission($this->getPermission());
        $this->registerArgument(0, new RawStringArgument("amount", false));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender instanceof Player)
            return;

        if ($sender->getSession()->getFaction() === null) {
            $sender->sendMessage(TextFormat::colorize('&cYou don\'t have a faction'));
            return;
        }

        $faction = Loader::getInstance()->getFactionManager()->getFaction($sender->getSession()->getFaction());


        if (!isset($args["amount"])) {
            $sender->sendMessage(TextFormat::colorize('&cUse /f w [amount]'));
            return;
        }

        $cantidad = $args["amount"];

        if ($cantidad < 0) {
            return;
        }

        if (!is_numeric($cantidad)) {
            $sender->sendMessage('§cUse /f w [amount]');
            return;
        }

        if($faction->getBalance() >= $cantidad) {
            $sender->getSession()->setBalance($sender->getSession()->getBalance() + (int)$cantidad);
            $sender->sendMessage('§aYour new balance is ' . $sender->getSession()->getBalance());
            $faction->setBalance($faction->getBalance() - (int)$cantidad);
        }else{
            $sender->sendMessage('§cThe amount you entered exceeds your faction balance!');
        }
    }

    public function getPermission(): ?string
    {
        return "use.player.command";
    }
}