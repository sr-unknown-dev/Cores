<?php

namespace hcf\command\faction\subcommands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\BaseSubCommand;
use hcf\faction\Faction;
use hcf\Loader;
use hcf\player\Player;
use pocketmine\command\CommandSender;
use pocketmine\item\VanillaItems;
use pocketmine\utils\TextFormat;

class SetHomeSubCommand extends BaseSubCommand
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
        if (!$sender instanceof Player)
            return;
        
        if ($sender->getSession()->getFaction() === null) {
            $sender->sendMessage(TextFormat::colorize('&cYou don\'t have a faction'));
            return;
        }
        $faction = Loader::getInstance()->getFactionManager()->getFaction($sender->getSession()->getFaction());
        
        if (!in_array($faction->getRole($sender->getXuid()), ['leader', 'co-leader'])) {
            $sender->sendMessage(TextFormat::colorize('&cYou aren\'t the leader or co-leader of the faction to set home'));
            return;
        }
        $claim = Loader::getInstance()->getClaimManager()->insideClaim($sender->getPosition());
        
        if ($claim === null || $claim->getName() !== $faction->getName()) {
            $sender->sendMessage(TextFormat::colorize('&cYou cannot place home outside your claim'));
            return;
        }
        $faction->setHome($sender->getPosition());
        foreach (Loader::getInstance()->getFactionManager()->getFaction($sender->getSession()->getFaction())->getOnlineMembers() as $onlineMember){
            $onlineMember->sendMessage(TextFormat::colorize('&g' . $sender->getName() . ' &fhas set a &l&gHome&r.'));
        }
    }

    public function getPermission(): ?string
    {
        return "use.player.command";
    }
}