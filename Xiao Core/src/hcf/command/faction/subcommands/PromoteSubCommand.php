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

class PromoteSubCommand extends BaseSubCommand
{
    public function __construct(string $name, string $description = "", array $aliases = [])
    {
        parent::__construct($name, $description, $aliases);
    }

    protected function prepare(): void
    {
        $this->setPermission($this->getPermission());
        $this->registerArgument(0, new PlayersArgument("player", false));
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

        if (!in_array($faction->getRole($sender->getXuid()), [Faction::LEADER, Faction::CO_LEADER])) {
            $sender->sendMessage(TextFormat::colorize('&cYou aren\'t the leader or co-leader of the invite member'));
            return;
        }

        if (!isset($args["player"])) {
            $sender->sendMessage(TextFormat::colorize('&cUse /f promote [player]'));
            return;
        }
        $session = null;
        $p = null;
        $player = $args["player"];
        
        if ($player instanceof Player) {
            if ($player->getId() === $sender->getId()) {
                $sender->sendMessage(TextFormat::colorize('&cYou can\'t promote yourself'));
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
                if ($member->getName() === $player) {
                    $session = $member;
                    break;
                }
            }
            
            if ($session === null) {
                $sender->sendMessage(TextFormat::colorize('&cMember not found'));
                return;
            }
        }
        
        if ($faction->getRole($session->getXuid()) === Faction::CO_LEADER) {
            $sender->sendMessage(TextFormat::colorize('&cYou can\'t promote this member'));
            return;
        }
        $roles = [
            Faction::MEMBER => Faction::CAPTAIN,
            Faction::CAPTAIN => Faction::CO_LEADER
        ];

        if ($faction->getRole($sender->getXuid()) === Faction::CO_LEADER) {
            if ($faction->getRole($session->getXuid()) === Faction::LEADER || $faction->getRole($session->getXuid()) === Faction::CO_LEADER) {
                $sender->sendMessage(TextFormat::colorize('&cYou can\'t promote this member'));
                return;
            }
        }
        $faction->addRole($session->getXuid(), $roles[$faction->getRole($session->getXuid())]);
        
        $sender->sendMessage(TextFormat::colorize('&aYou have promoted member ' . $session->getName()));
        
        if ($p !== null && $p->isOnline())
            $p->sendMessage(TextFormat::colorize('&aYou were promoted to ' . $faction->getRole($session->getXuid())));
    }

    public function getPermission(): ?string
    {
        return "use.player.command";
    }
}