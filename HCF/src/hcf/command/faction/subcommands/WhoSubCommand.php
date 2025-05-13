<?php

namespace hcf\command\faction\subcommands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use hcf\faction\Faction;
use hcf\Loader;
use hcf\player\Player;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class WhoSubCommand extends BaseSubCommand
{

    public function __construct(string $name, string $description = "", array $aliases = [])
    {
        parent::__construct($name, $description, $aliases);
    }

    protected function prepare(): void
    {
        $this->setPermission($this->getPermission());
        $this->registerArgument(0, new RawStringArgument("factionName", true));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender instanceof Player)
            return;
        $faction = null;

        if (!isset($args["factionName"])) {
            if ($sender->getSession()->getFaction() === null) {
                $sender->sendMessage(TextFormat::colorize('&cYou don\'t have faction'));
                return;
            }
            $faction = $sender->getSession()->getFaction();
        } else {
            $target = $sender->getServer()->getPlayerByPrefix($args["factionName"]);

            if ($target instanceof Player) {
                if ($target->getSession()->getFaction() === null) {
                    $sender->sendMessage(TextFormat::colorize('Player dont have faction'));
                    return;
                }
                $faction = $target->getSession()->getFaction();
            } else {
                if (Loader::getInstance()->getFactionManager()->getFaction($args["factionName"])) {
                    $faction = $args["factionName"];
                }
            }
        }

        if ($faction === null) {
            $sender->sendMessage(TextFormat::colorize('&cNo faction found'));
            return;
        }
        $message = '' . "\n";
        $message .= '&e' . (Loader::getInstance()->getFactionManager()->getFaction($faction)->getName()). ' &7[' . count(Loader::getInstance()->getFactionManager()->getFaction($faction)->getOnlineMembers()) . '/' . count(Loader::getInstance()->getFactionManager()->getFaction($faction)->getMembers()) . '] &a- &eHQ: &f' . (Loader::getInstance()->getFactionManager()->getFaction($faction)->getHome() !== null ? 'X: ' . Loader::getInstance()->getFactionManager()->getFaction($faction)->getHome()->getFloorX() . ' Z: ' . Loader::getInstance()->getFactionManager()->getFaction($faction)->getHome()->getFloorZ() : 'Not set ');
        $leaders =
        $message .=  "\n" . '&aLeader: &f' . implode(', ', array_map(function ($session) {
                return ($session->isOnline() ? '&a' : '&c') . $session->getName() . ' &7[' . $session->getKills() . ']';
            }, Loader::getInstance()->getFactionManager()->getFaction($faction)->getMembersByRole(Faction::LEADER))) . "\n";
        $message .= '&aColeaders: &f' . implode(', ', array_map(function ($session) {
                return ($session->isOnline() ? '&a' : '&c') . $session->getName() . ' &7[' . $session->getKills() . ']';
            }, Loader::getInstance()->getFactionManager()->getFaction($faction)->getMembersByRole(Faction::CO_LEADER))) . "\n";
        $message .= '&aCaptains: &f' . implode(', ', array_map(function ($session) {
                return ($session->isOnline() ? '&a' : '&c') . $session->getName() . ' &7[' . $session->getKills() . ']';
            }, Loader::getInstance()->getFactionManager()->getFaction($faction)->getMembersByRole(Faction::CAPTAIN))) . "\n";
        $message .= '&aMembers: &f' . implode(', ', array_map(function ($session) {
                return ($session->isOnline() ? '&a' : '&c') . $session->getName() . ' &7[' . $session->getKills() . ']';
            }, Loader::getInstance()->getFactionManager()->getFaction($faction)->getMembersByRole(Faction::MEMBER))) . "\n";
        $message .= '&aBalance: &a$' . Loader::getInstance()->getFactionManager()->getFaction($faction)->getBalance() . "\n";
        $message .= '&aDTR: ' . (Loader::getInstance()->getFactionManager()->getFaction($faction)->getDtr() >= Loader::getInstance()->getFactionManager()->getFaction($faction)->getMaxDtr() ? '&a' : (Loader::getInstance()->getFactionManager()->getFaction($faction)->getDtr() <= 0.00 ? '&c' : '&e')) . round(Loader::getInstance()->getFactionManager()->getFaction($faction)->getDtr(), 2) . '■' . "\n";
        if (Loader::getInstance()->getFactionManager()->getFaction($faction)->getDtr() < 0.1){
            $message .= '&aRaidable: &aYes'."\n";
        }else{
            $message .= '&aRaidable: &aNo'."\n";
        }

        if (Loader::getInstance()->getFactionManager()->getFaction($faction)->getTimeRegeneration() !== null) {
            $message .= '&aTime Until Regen: &f' . gmdate('H:i:s', Loader::getInstance()->getFactionManager()->getFaction($faction)->getTimeRegeneration()) . "\n";
        }
        $message .= '&aPoints: &f' . Loader::getInstance()->getFactionManager()->getFaction($faction)->getPoints() . "\n";
        $message .= '&aKoTH Captures: &f' . Loader::getInstance()->getFactionManager()->getFaction($faction)->getKothCaptures() . "\n";
        $message .= '&aStrikes: &f' . Loader::getInstance()->getFactionManager()->getFaction($faction)->getStrikes() . "\n";
        $message .= '';

        $sender->sendMessage(TextFormat::colorize($message));
    }

    public function getPermission(): ?string
    {
        return "use.player.command";
    }
}