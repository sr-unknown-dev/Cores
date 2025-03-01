<?php

namespace hcf\abilities\items;

use hcf\Loader;
use hcf\player\Player;
use hcf\utils\time\Timer;
use hcf\utils\Utils;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\scheduler\ClosureTask;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class Samurai implements Listener
{

    public function onItemUse(PlayerItemUseEvent $event): void
    {
        $item = $event->getItem();
        $player = $event->getPlayer();
        if ($player instanceof Player)
            if ($item->getNamedTag()->getTag("Abilities") !== null) {
                if ($item->getNamedTag()->getString("Abilities") === "Samurai") {
                    $event->cancel();
                    if ($player->getSession()->getCooldown('ability.Samurai') === null) {
                        if ($player->getSession()->getCooldown('ability.global') === null) {
                            if ($player->getSession()->getCooldown('starting.timer') !== null || $player->getSession()->getCooldown('pvp.timer') !== null) {
                                return;
                            }

                            if ($player->getCurrentClaim() === 'Spawn') {
                                return;
                            }

                            if ($player->getSession()->getCooldown('lastDamage') === null) {
                                $player->sendMessage(TextFormat::colorize("&cNo one has attacked you in the past 15 seconds!"));
                                return;
                            }

                            $cause = $player->getLastDamageCause();

                            
                            
                            if($cause === null) {
                                $player->sendMessage(TextFormat::colorize("&cNo one has attacked you in the past 15 seconds!"));
                            }
                            if (!$cause instanceof EntityDamageByEntityEvent) {
                                $player->sendMessage(TextFormat::colorize("&cNo one has attacked you in the past 15 seconds!"));
                                return;
                            }
                            $damager = $cause->getDamager();
                            if (!$damager instanceof Player) {
                                $player->sendMessage(TextFormat::colorize("&cNo one has attacked you in the past 15 seconds!"));
                            }

                            $player->sendMessage("§6You have activated §4Samurai");
                            Utils::PlaySound($player, "random.orb", 1, 1);
                            Loader::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($damager, $player): void {
                                $player->sendMessage(TextFormat::colorize("&gTeleporting in &65&cseconds..."));

                            }), 20 * 1);
                            Loader::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($damager, $player): void {
                                $player->sendMessage(TextFormat::colorize("&gTeleporting in &64&cseconds..."));

                            }), 20 * 2);
                            Loader::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($damager, $player): void {
                                $player->sendMessage(TextFormat::colorize("&gTeleporting in &63&cseconds..."));

                            }), 20 * 3);
                            Loader::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($damager, $player): void {
                                $player->sendMessage(TextFormat::colorize("&gTeleporting in &62&cseconds..."));

                            }), 20 * 4);
                            Loader::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($damager, $player): void {
                                $player->sendMessage(TextFormat::colorize("&gTeleporting in &61&cseconds..."));

                            }), 20 * 5);
                            Loader::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($damager, $player): void {
                                $player->sendMessage(TextFormat::colorize("&gTeleporting in &60&cseconds..."));

                            }), 20 * 6);
                            Loader::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($player, $damager): void {
                                $player->teleport($damager->getPosition());
                                $player->getSession()->addCooldown('ability.Samurai', ' §l§7×§r §4Samurai§r§f: §f', 120);
                                $player->getSession()->addCooldown('ability.global', ' §l§7×§r &5Partner Item§r§f: §f', 5);

                                if ($damager instanceof Player && $player instanceof Player){
                                    Utils::PlaySound($player, "random.anvil_use", 1, 1);
                                    $player->sendMessage(TextFormat::colorize("&7You've been &4teleported&7 to&f {$damager->getName()}."));
                                    $damager->sendMessage(TextFormat::colorize("&f{$player->getName()} &7has been &4teleported &7to you."));
                                    $player->getEffects()->add(new EffectInstance(VanillaEffects::STRENGTH(), 20 * 10, 1));
                                    $player->getEffects()->add(new EffectInstance(VanillaEffects::RESISTANCE(), 20 * 10, 1));
                                };

                                foreach (Server::getInstance()->getOnlinePlayers() as $players){
                                    $players->getNetworkSession()->sendDataPacket(Utils::addParticle($damager->getPosition(), "minecraft:end_chest"));
                                }
                                
                            }), 20 * 7);
                                
                            $item = $event->getItem();
                            $item->pop();
                            $player->getInventory()->setItemInHand($item);

                        } else {
                            $player->sendMessage("§6you have cooldown of §dPartner Item §f" . Timer::format($player->getSession()->getCooldown("ability.global")->getTime()));
                        }
                    } else {
                        $player->sendMessage("§cYou haven't Samurai in the last " . Timer::format($player->getSession()->getCooldown("ability.Samurai")->getTime()));
                    }
                }
            }
    }
}

