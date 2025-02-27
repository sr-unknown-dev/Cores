<?php

namespace hcf\abilities\items;

use hcf\abilities\entity\FrezzerGunEntity;
use hcf\Loader;
use hcf\item\EnderpearlItem;
use hcf\player\Player;
use hcf\utils\time\Timer;
use pocketmine\entity\Location;
use pocketmine\event\entity\ProjectileHitEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\TextFormat;
use pocketmine\block\VanillaBlocks;
use pocketmine\block\utils\MobHeadType;

class FrezzerGun implements Listener
{
    public function onItemUse(PlayerItemUseEvent $event): void
    {
        $item = $event->getItem();
        $player = $event->getPlayer();
        if ($player instanceof Player)
            if ($item->getNamedTag()->getTag("Abilities") !== null) {
                if ($item->getNamedTag()->getString("Abilities") === "FrezzerGun") {
                    $event->cancel();
                    if ($player->getSession()->getCooldown('ability.frezzergun') === null) {
                        if ($player->getSession()->getCooldown('ability.global') === null) {
                            if ($player->getSession()->getCooldown('starting.timer') !== null || $player->getSession()->getCooldown('pvp.timer') !== null) {
                                return;
                            }

                            if ($player->getCurrentClaim() === 'Spawn') {
                                return;
                            }
                            $entity = new FrezzerGunEntity(Location::fromObject($player->getEyePos(), $player->getWorld(), $player->getLocation()->getYaw(), $player->getLocation()->getPitch()), $player);
                            $entity->setMotion($event->getDirectionVector()->multiply(1.5));
                            $entity->spawnToAll();
                            $player->sendMessage("§6You have activated §4Freezer Gun");
                            $player->getSession()->addCooldown('ability.frezzergun', ' §l§7×§r §r§c|§r §4Freezer Gun§r§f: §f', 120);
                            $player->getSession()->addCooldown('ability.global', ' §l§7×§r &5Partner Item§r§f: §f', 5);
                            $item = $event->getItem();
                            $item->pop();
                            $player->getInventory()->setItemInHand($item);
                        } else {
                            $player->sendMessage("§6you have cooldown of §dPartner Item §f" . Timer::format($player->getSession()->getCooldown("ability.global")->getTime()));
                        }
                    } else {
                        $player->sendMessage("§cYou haven't Freezer hun in the last " . Timer::format($player->getSession()->getCooldown("ability.frezzergun")->getTime()));
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
            if ($player instanceof Player) {
                if ($entity instanceof FrezzerGunEntity) {
                    $hit->setNoClientPredictions(true);
                    $helmet = $hit->getArmorInventory()->getHelmet();
                    $hit->getArmorInventory()->setHelmet(VanillaBlocks::MOB_HEAD()->setMobHeadType(MobHeadType::SKELETON())->asItem());
                    $hit->setNameTag(TextFormat::AQUA . $hit->getName());
                    $hit->sendMessage(" ");
                    $hit->sendMessage("§6Acabas de recibir los efectos del §4Frezzer Gun");
                    Loader::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($hit, $helmet): void {
                        if ($hit->isOnline()) {
                            $hit->setNoClientPredictions(false);
                            $hit->getArmorInventory()->setHelmet($helmet);
                            $hit->setNameTag(TextFormat::RED . $hit->getName());
                        }
                    }), 20 * 5);
                }
            }
        }
    }
}