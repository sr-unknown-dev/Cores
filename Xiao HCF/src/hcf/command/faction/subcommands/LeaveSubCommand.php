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

class LeaveSubCommand extends BaseSubCommand
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

        if ($faction->getRole($sender->getXuid()) === Faction::LEADER) {
            $sender->sendMessage(TextFormat::colorize('&cYou are the faction Leader'));
            return;
        }
        
        if (Loader::getInstance()->getFactionManager()->getFaction($sender->getSession()->getFaction())->getTimeRegeneration() !== null) {
            $sender->sendMessage(TextFormat::colorize('&cYou can\'t use this with regeneration time active!'));
            return;
        }
        $faction->removeRole($sender->getXuid());
        
        $sender->getSession()->setFaction(null);
        $sender->getSession()->setFactionChat(false);
        
        $sender->setScoreTag('');
        $sender->sendMessage(TextFormat::colorize('&cYou just left your faction'));
    }

    public function getPermission(): ?string
    {
        return "use.player.command";
    }
}