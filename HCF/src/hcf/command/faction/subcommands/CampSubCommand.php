<?php

namespace hcf\command\faction\subcommands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use hcf\Loader;
use hcf\player\Player;
use pocketmine\command\CommandSender;
use pocketmine\scheduler\ClosureTask;
use pocketmine\Server;
use pocketmine\world\Position;

class CampSubCommand extends BaseSubCommand
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
        if (!$sender instanceof Player) {

          return;

      }
    
      if (count($args) < 1) {
          $sender->sendMessage("§cUsage: /f camp <faction>");
          return;
      }

      $factionName = $args["factionName"];
    
      $faction = Loader::getInstance()->getFactionManager()->getFaction($factionName);
    
      if ($faction === null) {
          $sender->sendMessage("§cFaction §e$factionName §cdoes not exist!"); 
          return;
      }
    
      $claim = Loader::getInstance()->getClaimManager()->getClaim($factionName);

      if ($claim === null) {
          $sender->sendMessage("§cFaction §e$factionName §cdoes not have a claim!");
          return; 
      }
      
      if ($sender->getSession()->getCooldown('camp.tag') !== null)
          return;
      
      $sender->getSession()->addCooldown('camp.tag',  ' §l§f|§r§cCamp&r&7: &c', Loader::getInstance()->getConfig()->get('camp.timer'));

      $x = $claim->getMinX(); 
      $z = $claim->getMinZ();

      $world = Server::getInstance()->getWorldManager()->getWorldByName("world");
      $y = $world->getHighestBlockAt($x, $z);
    
      $pos = new Position($x, $y + 1, $z, $world);
      
      $xuid = $sender->getXuid();
      $position = $sender->getPosition();
      $handler = null;
        $handler = Loader::getInstance()->getScheduler()->scheduleRepeatingTask(new ClosureTask(function () use (&$handler, &$sender, &$xuid, &$position, &$pos, &$factionName): void {
            $s = Loader::getInstance()->getSessionManager()->getSession($xuid);
            
            if (!$sender->isOnline()) {
                if ($s->getCooldown('camp.tag') !== null) $s->removeCooldown('camp.tag');
                $handler?->cancel();
                return;
            }

            if ($s->getCooldown('spawn.tag') !== null) {
                $s->removeCooldown('camp.tag');
                $handler?->cancel();
                return;
            }
            
            if ($position->distance($sender->getPosition()) > 2) {
                if ($s->getCooldown('camp.tag') !== null) $s->removeCooldown('camp.tag');
                $handler?->cancel();
                return;
            }
            
            if ($sender->getSession()->getCooldown('camp.tag') === null) {
                $sender->teleport($pos);
    			$sender->sendMessage("§aTeleported near §e$factionName's claim!");
                $handler?->cancel();
            }
        }), 20);
    }

    public function getPermission(): ?string
    {
        return "use.player.command";
    }
}