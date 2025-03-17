<?php

namespace hcf\abilities\items;

use hcf\abilities\entity\BallOfRangeEntity;
use hcf\abilities\entity\Cerdito1Entity;
use hcf\abilities\entity\Cerdito2Entity;
use hcf\abilities\entity\Cerdito3Entity;
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

class PortablePigs implements Listener
{
    public function onItemUse(PlayerItemUseEvent $event): void
    {
        $item = $event->getItem();
        $player = $event->getPlayer();
        if ($player instanceof Player)
            if ($item->getNamedTag()->getTag("Abilities") !== null) {
                if ($item->getNamedTag()->getString("Abilities") === "PortablePigs") {
                    $event->cancel();
                    if ($player->getSession()->getCooldown('ability.Portablepigs') === null) {
                        if ($player->getSession()->getCooldown('ability.global') === null) {
                            if ($player->getSession()->getCooldown('starting.timer') !== null || $player->getSession()->getCooldown('pvp.timer') !== null) {
                                return;
                            }

                            if ($player->getCurrentClaim() === 'Spawn') {
                                return;
                            }
                            $strength = new Cerdito1Entity($event->getPlayer()->getLocation());
                            $strength->setOwner($event->getPlayer());
                            $strength->setPos($event->getPlayer()->getLocation());
                            $strength->spawnToAll();
                            $resis = new Cerdito2Entity($event->getPlayer()->getLocation());
                            $resis->setOwner($event->getPlayer());
                            $resis->setPos($event->getPlayer()->getLocation());
                            $resis->spawnToAll();
                            $speed = new Cerdito3Entity($event->getPlayer()->getLocation());
                            $speed->setOwner($event->getPlayer());
                            $speed->setPos($event->getPlayer()->getLocation());
                            $speed->spawnToAll();
                            $player->sendMessage("§6You have activated §cBall of Range");
                            $player->getEffects()->add(new EffectInstance(VanillaEffects::STRENGTH(), 10 * 20, 1));
                            $player->getEffects()->add(new EffectInstance(VanillaEffects::RESISTANCE(), 10 * 20, 1));
                            $player->getEffects()->add(new EffectInstance(VanillaEffects::SPEED(), 10 * 20, 2));
                            $player->getSession()->addCooldown('ability.Portablepigs', ' §l§7×§r §bEffect Bard§r§f: §f', 120);
                            $player->getSession()->addCooldown('ability.global', ' §l§7×§r &5Partner Item§r§f: §f', 5);
                            $item = $event->getItem();
                            $item->pop();
                            $player->getInventory()->setItemInHand($item);

                        } else {
                            $player->sendMessage("§6you have cooldown of §dPartner Item §f" . Timer::format($player->getSession()->getCooldown("ability.global")->getTime()));
                        }
                    } else {
                        $player->sendMessage("§cYou haven't PortablePigs in the last " . Timer::format($player->getSession()->getCooldown("ability.Portablepigs")->getTime()));
                    }
                }
            }
    }
}