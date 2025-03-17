<?php

namespace hcf\abilities\items;

use hcf\Loader;
use hcf\item\EnderpearlItem;
use hcf\player\Player;
use hcf\utils\time\Timer;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\TextFormat;

class EffectDisabler implements Listener
{
    public $data;
    public function onDamage(EntityDamageByEntityEvent $event): void
    {
        $victim = $event->getEntity();
        $player = $event->getDamager();
        if ($victim instanceof Player && $player instanceof Player) {
            $item = $player->getInventory()->getItemInHand();
            if ($item->getNamedTag()->getTag("Abilities") !== null) {
                if ($item->getNamedTag()->getString("Abilities") === "EffectDisabler") {
                    if ($player->getSession()->getCooldown('ability.effectdisabler') === null) {
                        if ($player->getSession()->getCooldown('ability.global') === null) {

                            $effect = $victim->getEffects()->all();

                            if ($player->getSession()->getCooldown('starting.timer') !== null || $player->getSession()->getCooldown('pvp.timer') !== null) {
                                return;
                            }

                            if ($victim->getSession()->getCooldown('starting.timer') !== null || $victim->getSession()->getCooldown('pvp.timer') !== null) {
                                return;
                            }

                            if ($victim->getSession()->getFaction() !== null && $player->getSession()->getFaction() !== null) {
                                if ($victim->getSession()->getFaction() === $player->getSession()->getFaction()) {
                                    $player->sendMessage(TextFormat::colorize("§eYou cannot hurt §2" . $victim->getName() . "§e."));
                                    $event->cancel();
                                    return;
                                }
                            }

                            if ($player->getCurrentClaim() === 'Spawn') {
                                return;
                            }

                            if (count($effect) <= 0) {
                                $player->sendMessage("§cEl Jugador " . $victim->getName() . " no tienen ningun efecto.");
                                return;
                            }

                            if (!isset($this->data[$player->getName()])) {
                                $this->data[$player->getName()] = 1;
                            }
                
                            $this->data[$player->getName()]++;
                
                            if($this->data[$player->getName()] < 3)return;
                                    $victim->getEffects()->clear();
                                    Loader::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($victim, $effect): void {
                                        if ($victim->isOnline()) {
                                            $victim->getEffects()->clear();
                                        }
                                    }), 20 * 0.5);
                                    Loader::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($victim, $effect): void {
                                        if ($victim->isOnline()) {
                                            $victim->getEffects()->clear();
                                        }
                                    }), 20 * 1);
                                    Loader::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($victim, $effect): void {
                                        if ($victim->isOnline()) {
                                            $victim->getEffects()->clear();
                                        }
                                    }), 20 * 1.5);
                                    Loader::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($victim, $effect): void {
                                        if ($victim->isOnline()) {
                                            $victim->getEffects()->clear();
                                        }
                                    }), 20 * 2);
                                    Loader::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($victim, $effect): void {
                                        if ($victim->isOnline()) {
                                            $victim->getEffects()->clear();
                                        }
                                    }), 20 * 2.5);
                                    Loader::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($victim, $effect): void {
                                        if ($victim->isOnline()) {
                                            $victim->getEffects()->clear();
                                        }
                                    }), 20 * 3);
                                    Loader::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($victim, $effect): void {
                                        if ($victim->isOnline()) {
                                            $victim->getEffects()->clear();
                                        }
                                    }), 20 * 3.5);
                                    Loader::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($victim, $effect): void {
                                        if ($victim->isOnline()) {
                                            $victim->getEffects()->clear();
                                        }
                                    }), 20 * 4);
                                    Loader::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($victim, $effect): void {
                                        if ($victim->isOnline()) {
                                            $victim->getEffects()->clear();
                                        }
                                    }), 20 * 4.5);
                                    Loader::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($victim, $effect): void {
                                        if ($victim->isOnline()) {
                                            $victim->getEffects()->clear();
                                        }
                                    }), 20 * 5);
                                    Loader::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($victim, $effect): void {
                                        if ($victim->isOnline()) {
                                            $victim->getEffects()->clear();
                                        }
                                    }), 20 * 5.5);
                                    Loader::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($victim, $effect): void {
                                        if ($victim->isOnline()) {
                                            foreach ($effect as $e) {
                                                $victim->getEffects()->add($e);
                                            }
                                        }
                                    }), 20 * 6);
                                    $player->sendMessage("§6You have activated §aEffects Disabler in " . $victim->getName());
                                    $player->getSession()->addCooldown('ability.effectdisabler', ' §l§7×§r §aEffects Disabler§r§f: §f', 120);
                                    $player->getSession()->addCooldown('ability.global', ' §l§7×§r &5Partner Item§r§f: §f', 5);

                            if($item->getCount() > 1){
                                $item->setCount($item->getCount() - 1);
                            } else {
                                $item = VanillaItems::AIR();
                            }
                            $player->getInventory()->setItemInHand($item);

                        } else {
                            $player->sendMessage("§6you have cooldown of §dPartner Item §f" . Timer::format($player->getSession()->getCooldown("ability.global")->getTime()));
                        }
                    } else {
                        $player->sendMessage("§cYou can't use the Effects Disabler in the last " . Timer::format($player->getSession()->getCooldown("ability.effectdisabler")->getTime()));
                    }
                }
            }
        }
    }
}
