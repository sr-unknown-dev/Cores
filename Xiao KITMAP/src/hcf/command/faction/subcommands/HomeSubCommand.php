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
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\TaskHandler;

class HomeSubCommand extends BaseSubCommand
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
        
        if ($faction->getHome() === null) {
            $sender->sendMessage(TextFormat::colorize('&cYour faction has no home'));
            return;
        }
        
        if ($sender->getSession()->getCooldown('faction.teleport.home') !== null)
            return;

        if ($sender->getSession()->getCooldown('pvp.timer') !== null || $sender->getSession()->getCooldown('starting.timer') !== null) {
            $sender->sendMessage(TextFormat::colorize('§l§a* §r§c You have PvP Timer§7!!'));
            return;
        }
        
        if ($sender->getCurrentClaim() === 'Spawn') {
            $sender->teleport($faction->getHome());
            return;
        }
        $sender->getSession()->addCooldown('faction.teleport.home',  ' §l§f|§r§cHome&r&7: &c', 15);
        
        $xuid = $sender->getXuid();
        $position = $sender->getPosition();
        /** @var TaskHandler */
        $handler = null;
        $handler = Loader::getInstance()->getScheduler()->scheduleRepeatingTask(new ClosureTask(function () use (&$handler, &$sender, &$xuid, &$position): void {
            $s = Loader::getInstance()->getSessionManager()->getSession($xuid);
            $faction = Loader::getInstance()->getFactionManager()->getFaction($s->getFaction());
            
            if (!$sender->isOnline()) {
                if ($s->getCooldown('faction.teleport.home') !== null) $s->removeCooldown('faction.teleport.home');
                $handler->cancel();
                return;
            }
            
            if ($faction === null) {
                if ($s->getCooldown('faction.teleport.home') !== null) $s->removeCooldown('faction.teleport.home');
                $handler->cancel();
                return;
            }
            
            if ($position->distance($sender->getPosition()) > 2) {
                if ($s->getCooldown('faction.teleport.home') !== null) $s->removeCooldown('faction.teleport.home');
                $handler->cancel();
                return;
            }
            
            if ($sender->getSession()->getCooldown('spawn.tag') !== null) {
                if ($s->getCooldown('faction.teleport.home') !== null) $s->removeCooldown('faction.teleport.home');
                $handler->cancel();
                return;
            }
            
            if ($faction->getHome() === null) {
                if ($s->getCooldown('faction.teleport.home') !== null) $s->removeCooldown('faction.teleport.home');
                $handler->cancel();
                return;
            }
            
            if ($sender->getSession()->getCooldown('faction.teleport.home') === null) {
                $sender->teleport($faction->getHome());
                $handler->cancel();
            }
        }), 20);
    }
    
    public function getPermission(): ?string
    {
        return "use.player.command";
    }
}