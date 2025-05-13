<?php

namespace hcf\command\faction\subcommands;

use CortexPE\Commando\BaseSubCommand;
use hcf\faction\Faction;
use hcf\Loader;
use hcf\player\Player;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class InfoSubCommand extends BaseSubCommand
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
        if ($sender instanceof Player) {
            
            if ($sender->getSession()->getFaction() === null) {
                $sender->sendMessage(TextFormat::RED."No tienes una faction");
            }

        $faction = $sender->getSession()->getFaction();
        $message = '' . "\n";
        $message .= '&e' . (Loader::getInstance()->getFactionManager()->getFaction($faction)->getName()). ' &7[' . count(Loader::getInstance()->getFactionManager()->getFaction($faction)->getOnlineMembers()) . '/' . count(Loader::getInstance()->getFactionManager()->getFaction($faction)->getMembers()) . '] &4- &eHQ: &f' . (Loader::getInstance()->getFactionManager()->getFaction($faction)->getHome() !== null ? 'X: ' . Loader::getInstance()->getFactionManager()->getFaction($faction)->getHome()->getFloorX() . ' Z: ' . Loader::getInstance()->getFactionManager()->getFaction($faction)->getHome()->getFloorZ() : 'Not set ');
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
        $message .= '&aBalance: &9$' . Loader::getInstance()->getFactionManager()->getFaction($faction)->getBalance() . "\n";
        $message .= '&aDTR: ' . (Loader::getInstance()->getFactionManager()->getFaction($faction)->getDtr() >= Loader::getInstance()->getFactionManager()->getFaction($faction)->getMaxDtr() ? '&a' : (Loader::getInstance()->getFactionManager()->getFaction($faction)->getDtr() <= 0.00 ? '&c' : '&e')) . round(Loader::getInstance()->getFactionManager()->getFaction($faction)->getDtr(), 2) . '■' . "\n";
        if (Loader::getInstance()->getFactionManager()->getFaction($faction)->getDtr() < 0.1){
            $message .= '&aRaidable: &aYes'."\n";
        }else{
            $message .= '&aRaidable: &4No'."\n";
        }

        if (Loader::getInstance()->getFactionManager()->getFaction($faction)->getTimeRegeneration() !== null) {
            $message .= '&aTime Until Regen: &9' . gmdate('H:i:s', Loader::getInstance()->getFactionManager()->getFaction($faction)->getTimeRegeneration()) . "\n";
        }
        $message .= '&aPoints: &c' . Loader::getInstance()->getFactionManager()->getFaction($faction)->getPoints() . "\n";
        $message .= '&aKoTH Captures: &c' . Loader::getInstance()->getFactionManager()->getFaction($faction)->getKothCaptures() . "\n";
        $message .= '&aStrikes: &c' . Loader::getInstance()->getFactionManager()->getFaction($faction)->getStrikes() . "\n";
        $message .= '';

        $sender->sendMessage(TextFormat::colorize($message));
        }
    }

    public function getPermission(): ?string
    {
        return "use.player.command";
    }
}