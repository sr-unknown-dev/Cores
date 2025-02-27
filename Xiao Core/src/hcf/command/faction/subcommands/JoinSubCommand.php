<?php

namespace hcf\command\faction\subcommands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\BaseSubCommand;
use hcf\arguments\FactionsArgument;
use hcf\faction\Faction;
use hcf\faction\FactionInvite;
use hcf\Loader;
use hcf\player\Player;
use pocketmine\command\CommandSender;
use pocketmine\item\VanillaItems;
use pocketmine\utils\TextFormat;

class JoinSubCommand extends BaseSubCommand
{
    public function __construct(string $name, string $description = "", array $aliases = [])
    {
        parent::__construct($name, $description, $aliases);
    }

    protected function prepare(): void
    {
        $this->setPermission($this->getPermission());
        $this->registerArgument(0, new RawStringArgument("factionName", false));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender instanceof Player)
            return;
        
        if ($sender->getSession()->getFaction() !== null) {
            $sender->sendMessage(TextFormat::colorize('&cYou have already faction'));
            return;
        }
        
        if (isset($args["factionName"])) {
            $factionName = (string) $args["factionName"];
            $playerInvites = Loader::getInstance()->getFactionManager()->getInvites($sender->getXuid());
            
            if ($playerInvites === null || count($playerInvites) === 0) {
                $sender->sendMessage(TextFormat::colorize('&cYou don\'t have invites'));
                return;
            }
            $invites = array_filter($playerInvites, function (FactionInvite $invite): bool {
                return $invite->getTime() > time();
            });

            if (!isset($invites[$factionName])) {
                $sender->sendMessage(TextFormat::colorize('&cYou have no invites from this faction'));
                return;
            }
            $invite = $invites[$factionName];
            
            if ($invite->getTime() < time()) {
                $sender->sendMessage(TextFormat::colorize('&cThis invite has already expired'));
                Loader::getInstance()->getFactionManager()->removeInvite($sender, $factionName);
                return;
            }
            
            if ($invite->getPlayer()->getSession()->getFaction() !== $invite->getFaction()) {
                $sender->sendMessage(TextFormat::colorize('&cInvite not valid'));
                Loader::getInstance()->getFactionManager()->removeInvite($sender, $factionName);
                return;
            }
            $faction = Loader::getInstance()->getFactionManager()->getFaction($factionName);
    
            if ($faction->getRole($invite->getPlayer()->getName()) === Faction::MEMBER) {
                $sender->sendMessage(TextFormat::colorize('&cInvite not valid'));
                Loader::getInstance()->getFactionManager()->removeInvite($sender, $factionName);
                return;
            }
            $player = $invite->getPlayer();
            
            if ($player->isOnline()) {
                $player->sendMessage(TextFormat::colorize('&a' . $sender->getName() . ' accepted invitation for join in your faction'));
            }
            $sender->sendMessage(TextFormat::colorize('&aYou have accepted ' . $player->getName() . '\' invite for join in faction'));
            
            $faction->addRole($sender->getXuid(), Faction::MEMBER);
            $faction->announce(TextFormat::colorize('&a' . $sender->getName() . ' joined the faction'));
            $faction->setDtr(0.01 + (count($faction->getMembers()) * 1.00));
            
            $sender->setScoreTag(TextFormat::colorize('&6[&c' . $faction->getName() . ' ' . ($faction->getDtr() === (count($faction->getMembers()) + 0.1) ? '&a' : '&c') . $faction->getDtr() . '■&6]'));
            $sender->getSession()->setFaction($faction->getName());
    
            Loader::getInstance()->getFactionManager()->removeInvite($sender, $factionName);
            return;
        }
    }

    public function getPermission(): ?string
    {
        return "use.player.command";
    }
}