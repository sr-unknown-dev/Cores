<?php

declare(strict_types=1);

namespace hcf\faction\command\subcommand;

use hcf\faction\command\FactionSubCommand;
use hcf\faction\Faction;
use hcf\Loader;
use hcf\player\Player;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class WhoSubCommand implements FactionSubCommand
{

    /**
     * @param CommandSender $sender
     * @param array $args
     */
    public function execute(CommandSender $sender, array $args): void
    {
        if (!$sender instanceof Player)
            return;
        $faction = null;

        if (!isset($args[0])) {
            if ($sender->getSession()->getFaction() === null) {
                $sender->sendMessage(TextFormat::colorize('&cYou don\'t have faction'));
                return;
            }
            $faction = $sender->getSession()->getFaction();
        } else {
            $target = $sender->getServer()->getPlayerByPrefix($args[0]);

            if ($target instanceof Player) {
                if ($target->getSession()->getFaction() === null) {
                    $sender->sendMessage(TextFormat::colorize('Player dont have faction'));
                    return;
                }
                $faction = $target->getSession()->getFaction();
            } else {
                if (Loader::getInstance()->getFactionManager()->getFaction($args[0])) {
                    $faction = $args[0];
                }
            }
        }

        if ($faction === null) {
            $sender->sendMessage(TextFormat::colorize('&cNo faction found'));
            return;
        }
        $message = '' . "\n";
        $message .= '&e' . (Loader::getInstance()->getFactionManager()->getFaction($faction)->getName()). ' &7[' . count(Loader::getInstance()->getFactionManager()->getFaction($faction)->getOnlineMembers()) . '/' . count(Loader::getInstance()->getFactionManager()->getFaction($faction)->getMembers()) . '] &4- &eHQ: &f' . (Loader::getInstance()->getFactionManager()->getFaction($faction)->getHome() !== null ? 'X: ' . Loader::getInstance()->getFactionManager()->getFaction($faction)->getHome()->getFloorX() . ' Z: ' . Loader::getInstance()->getFactionManager()->getFaction($faction)->getHome()->getFloorZ() : 'Not set ');
        $leaders = 
        $message .=  "\n" . '&aLeader: &f' . implode(', ', array_map(function ($session) {
            return ($session->isOnline() ? '&a' : '&c') . $session->getName() . ' &7[' . $session->getKills() . ']';
        }, Loader::getInstance()->getFactionManager()->getFaction($faction)->getMembersByRole(Faction::LEADER))) . "\n";
        $message .= '&4Coleaders: &f' . implode(', ', array_map(function ($session) {
            return ($session->isOnline() ? '&a' : '&c') . $session->getName() . ' &7[' . $session->getKills() . ']';
        }, Loader::getInstance()->getFactionManager()->getFaction($faction)->getMembersByRole(Faction::CO_LEADER))) . "\n";
        $message .= '&4Captains: &f' . implode(', ', array_map(function ($session) {
            return ($session->isOnline() ? '&a' : '&c') . $session->getName() . ' &7[' . $session->getKills() . ']';
        }, Loader::getInstance()->getFactionManager()->getFaction($faction)->getMembersByRole(Faction::CAPTAIN))) . "\n";
        $message .= '&4Members: &f' . implode(', ', array_map(function ($session) {
            return ($session->isOnline() ? '&a' : '&c') . $session->getName() . ' &7[' . $session->getKills() . ']';
        }, Loader::getInstance()->getFactionManager()->getFaction($faction)->getMembersByRole(Faction::MEMBER))) . "\n";
        $message .= '&4Balance: &9$' . Loader::getInstance()->getFactionManager()->getFaction($faction)->getBalance() . "\n";
        $message .= '&gDTR: ' . (Loader::getInstance()->getFactionManager()->getFaction($faction)->getDtr() >= Loader::getInstance()->getFactionManager()->getFaction($faction)->getMaxDtr() ? '&a' : (Loader::getInstance()->getFactionManager()->getFaction($faction)->getDtr() <= 0.00 ? '&c' : '&e')) . round(Loader::getInstance()->getFactionManager()->getFaction($faction)->getDtr(), 2) . '■' . "\n";

        if (Loader::getInstance()->getFactionManager()->getFaction($faction)->getTimeRegeneration() !== null) {
            $message .= '&3Time Until Regen: &9' . gmdate('H:i:s', Loader::getInstance()->getFactionManager()->getFaction($faction)->getTimeRegeneration()) . "\n";
        }
        $message .= '&4Points: &c' . Loader::getInstance()->getFactionManager()->getFaction($faction)->getPoints() . "\n";
        $message .= '&4KoTH Captures: &c' . Loader::getInstance()->getFactionManager()->getFaction($faction)->getKothCaptures() . "\n";
        $message .= '&dStrikes: &c' . Loader::getInstance()->getFactionManager()->getFaction($faction)->getStrikes() . "\n";
        $message .= '';

        $sender->sendMessage(TextFormat::colorize($message));
    }
}