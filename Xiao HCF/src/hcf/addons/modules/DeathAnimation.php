<?php

namespace hcf\addons\modules;

use hcf\player\Player;
use hcf\utils\Utils;
use pocketmine\block\VanillaBlocks;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\types\LevelSoundEvent;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use pocketmine\world\particle\BlockBreakParticle;

class DeathAnimation implements Listener
{
    /**
     * @param PlayerDeathEvent $event
     */
    public function AddAnimation(PlayerDeathEvent $event): void
    {
        foreach (Server::getInstance()->getOnlinePlayers() as $player){
            $p = $event->getPlayer();
            $world = $p->getWorld();
            $pos = $p->getPosition()->floor();
            for($y = 0; $y < 2; $y++) {
                $player->getNetworkSession()->sendDataPacket(Utils::addParticle($pos, "minecraft:huge_explosion_emitter"));
                $player->getNetworkSession()->sendDataPacket(Utils::addSound($pos, LevelSoundEvent::EXPLODE));
            }
        }
    }
}