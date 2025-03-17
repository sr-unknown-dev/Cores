<?php

namespace hcf\abilities\items;

use hcf\abilities\entity\BallOfRangeEntity;
use hcf\player\Player;
use hcf\utils\time\Timer;
use hcf\utils\Utils;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\entity\Location;
use pocketmine\event\entity\ProjectileHitEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\network\mcpe\protocol\types\LevelSoundEvent;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class BallOfRange implements Listener
{
    public function onItemUse(PlayerItemUseEvent $event): void
    {
        $item = $event->getItem();
        $player = $event->getPlayer();
        if ($player instanceof Player)
            if ($item->getNamedTag()->getTag("Abilities") !== null) {
                if ($item->getNamedTag()->getString("Abilities") === "BallOfRange") {
                    $event->cancel();
                    if ($player->getSession()->getCooldown('ability.Ballofrange') === null) {
                        if ($player->getSession()->getCooldown('ability.global') === null) {
                            if ($player->getSession()->getCooldown('starting.timer') !== null || $player->getSession()->getCooldown('pvp.timer') !== null) {
                                return;
                            }

                            if ($player->getCurrentClaim() === 'Spawn') {
                                return;
                            }
                            $entity = new BallOfRangeEntity(Location::fromObject($player->getEyePos(), $player->getWorld(), $player->getLocation()->getYaw(), $player->getLocation()->getPitch()), $player);
                            $entity->setMotion($event->getDirectionVector()->multiply(1.5));
                            $entity->spawnToAll();
                            $player->sendMessage("§6You have activated §cBall of Range");
                            $player->getEffects()->add(new EffectInstance(VanillaEffects::STRENGTH(), 8 * 20, 1));
                            $player->getEffects()->add(new EffectInstance(VanillaEffects::RESISTANCE(), 8 * 20, 2));
                            $player->getSession()->addCooldown('ability.Ballofrange', ' §l§7×§r §3Ball Of Range§r§f: §f', 20);
                            $player->getSession()->addCooldown('ability.global', ' §l§7×§r &5Partner Item§r§f: §f', 5);
                            $item = $event->getItem();
                            $item->pop();
                            $player->getInventory()->setItemInHand($item);

                        } else {
                            $player->sendMessage("§6you have cooldown of §dPartner Item §f" . Timer::format($player->getSession()->getCooldown("ability.global")->getTime()));
                        }
                    } else {
                        $player->sendMessage("§cYou haven't Ball Of Range in the last " . Timer::format($player->getSession()->getCooldown("ability.Ballofrange")->getTime()));
                    }
                }
            }
    }

    public function onHitByProjectile(ProjectileHitEntityEvent $event) : void
    {
        $hit = $event->getEntityHit();
        if ($hit instanceof Player) {
            $entity = $event->getEntity();
            $player = $entity->getOwningEntity();
            $hitTraceResult = $event->getRayTraceResult();
            if ($player instanceof Player) {
                if ($entity instanceof BallOfRangeEntity) {
                    if ($player->getSession()->getCooldown('starting.timer') !== null || $player->getSession()->getCooldown('pvp.timer') !== null) {
                        return;
                    }

                    if ($player->getCurrentClaim() === 'Spawn') {
                        return;
                    }
                    if ($hit->getSession()->getCooldown('starting.timer') !== null || $hit->getSession()->getCooldown('pvp.timer') !== null) {
                        return;
                    }

                    if ($hit->getCurrentClaim() === 'Spawn') {
                        return;
                    }
                    $pos1 = $player->getPosition();
                    $pos2 = $hit->getPosition();
                    $hit->getEffects()->add(new EffectInstance(VanillaEffects::WEAKNESS(), 5 * 20, 1));
                    $hit->getEffects()->add(new EffectInstance(VanillaEffects::WITHER(), 5 * 20, 1));
                    foreach (Server::getInstance()->getOnlinePlayers() as $players){
                        $players->getNetworkSession()->sendDataPacket(Utils::addParticle($hitTraceResult->getHitVector(), "minecraft:huge_explosion_emitter"));
                        $players->getNetworkSession()->sendDataPacket(Utils::addSound($hitTraceResult->getHitVector(), LevelSoundEvent::EXPLODE));
                    }
                }
            }
        }
    }
}