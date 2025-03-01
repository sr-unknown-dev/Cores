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
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\TextFormat;
use pocketmine\world\Position;

class StuckSubCommand extends BaseSubCommand
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
            
        if ($sender->getSession()->getCooldown('faction.stuck') !== null)
            return;
        $sender->getSession()->addCooldown('faction.stuck',  '§l§g|§r §5Stuck&r&7: &c', 45);
        
        $xuid = $sender->getXuid();
        $position = $sender->getPosition();
        /** @var TaskHandler */
        $handler = null;
        $handler = Loader::getInstance()->getScheduler()->scheduleRepeatingTask(new ClosureTask(function () use (&$handler, &$sender, &$xuid, &$position): void {
            $s = Loader::getInstance()->getSessionManager()->getSession($xuid);
            
            if (!$sender->isOnline()) {
                if ($s->getCooldown('faction.stuck') !== null) $s->removeCooldown('faction.stuck');
                $handler->cancel();
                return;
            }
            
            if ($position->distance($sender->getPosition()) > 2) {
                if ($s->getCooldown('faction.stuck') !== null) $s->removeCooldown('faction.stuck');
                $handler->cancel();
                return;
            }

            
            if ($sender->getSession()->getCooldown('spawn.tag') !== null){
                $handler->cancel();
                return;
            }

            if ($sender->getSession()->getCooldown('faction.stuck') === null) {
                $this->teleport($sender);
                $handler->cancel();
            }
        }), 20);
    }

    public function getPermission(): ?string
    {
        return "use.player.command";
    }

    private function teleport(Player $player): void
    {
        $world = $player->getWorld();
        $x = mt_rand($player->getPosition()->getFloorX() - 100, $player->getPosition()->getFloorX() + 100);
        $z = mt_rand($player->getPosition()->getFloorZ() - 100, $player->getPosition()->getFloorZ() + 100);
        $y = $world->getHighestBlockAt($x, $z);
        
        $position = new Position($x, $y, $z, $world);
        
        if (($claim = Loader::getInstance()->getClaimManager()->insideClaim($position)) !== null) {
            $this->teleport($player);
            return;
        }
        $player->teleport($position->add(0, 1, 0));
    }
}