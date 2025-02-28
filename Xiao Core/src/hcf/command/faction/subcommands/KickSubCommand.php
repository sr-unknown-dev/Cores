<?php

namespace hcf\command\faction\subcommands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\BaseSubCommand;
use hcf\arguments\PlayersArgument;
use hcf\faction\Faction;
use hcf\Loader;
use hcf\player\Player;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use pocketmine\Server;

class KickSubCommand extends BaseSubCommand
{
    public function __construct(string $name, string $description = "", array $aliases = [])
    {
        parent::__construct($name, $description, $aliases);
    }

    protected function prepare(): void
    {
        $this->setPermission($this->getPermission());
        $this->registerArgument(0, new RawStringArgument("player", true));
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
        
        if (!in_array($faction->getRole($sender->getXuid()), [Faction::LEADER, Faction::CO_LEADER, Faction::CAPTAIN])) {
            $sender->sendMessage(TextFormat::colorize('&cYou aren\'t the leader, co-leader or captain of the invite member'));
            return;
        }
        
        if (Loader::getInstance()->getFactionManager()->getFaction($sender->getSession()->getFaction())->getTimeRegeneration() !== null) {
            $sender->sendMessage(TextFormat::colorize('&cYou can\'t use this with regeneration time active!'));
            return;
        }
        
        if (!isset($args["player"])) {
            $sender->sendMessage(TextFormat::colorize('&cUse /f kick [player]'));
            return;
        }
        $session = null;
        $p = null;
        $player = $args["player"];
        
        if ($player instanceof Player) {
            if ($player->getId() === $sender->getId()) {
                $sender->sendMessage(TextFormat::colorize('&cYou can\'t kick yourself'));
                return;
            }
            
            if ($player->getSession()->getFaction() !== $faction->getName()) {
                $sender->sendMessage(TextFormat::colorize('&cThe player is not a member'));
                return;
            }
            $session = $player->getSession();
            $p = $player;
        } else {
            $members = $faction->getMembers();
            
            foreach ($members as $member) {
                if ($member->getName() === $args["player"]) {
                    $session = $member;
                    break;
                }
            }
            
            if ($session === null) {
                $sender->sendMessage(TextFormat::colorize('&cMember not found'));
                return;
            }
        }
        
        if ($faction->getRole($sender->getXuid()) === Faction::CO_LEADER) {
            if ($faction->getRole($session->getXuid()) === Faction::LEADER || $faction->getRole($session->getXuid()) === Faction::CO_LEADER) {
                $sender->sendMessage(TextFormat::colorize('&cYou cannot kick this player'));
                return;
            }
        }
        $faction->removeRole($session->getXuid());
        $faction->setDtr(0.01 + (count($faction->getMembers()) * 1.00));
        
        $session->setFactionChat(false);
        $session->setFaction(null);
        
        if ($p !== null && $p->isOnline()) {
            $p->setScoreTag('');
            $p->sendMessage(TextFormat::colorize('&cYou were kicked out of your faction'));
        }
        $sender->sendMessage(TextFormat::colorize('&cYou kicked the player'));
    }

    public function getPermission(): ?string
    {
        return "use.player.command";
    }
}