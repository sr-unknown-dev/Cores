<?php

namespace hcf\abilities\items;

use hcf\abilities\Hits;
use hcf\abilities\Trueno;
use hcf\Loader;
use hcf\player\Player;
use hcf\Tasks\ThorTask;
use hcf\utils\time\Timer;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\network\mcpe\protocol\types\entity\PropertySyncData;

class Thor implements Listener
{
    use Trueno;
    private $hitCount = [];
    private $data;

    public function onDamage(EntityDamageByEntityEvent $event): void
    {
        $victim = $event->getEntity();
        $player = $event->getDamager();
        $position = $victim->getPosition();
        $runtimeId = $victim->getId();
        if ($victim instanceof Player && $player instanceof Player) {
            $item = $player->getInventory()->getItemInHand();
            if ($item->getNamedTag()->getTag("Abilities") !== null) {
                if ($item->getNamedTag()->getString("Abilities") === "Thor") {
                    if ($player->getSession()->getCooldown('ability.thor') === null) {
                        if ($player->getSession()->getCooldown('ability.global') === null) {

                            if ($player->getSession()->getCooldown('starting.timer') !== null || $player->getSession()->getCooldown('pvp.timer') !== null) {
                                return;
                            }

                            if ($victim->getSession()->getCooldown('starting.timer') !== null || $victim->getSession()->getCooldown('pvp.timer') !== null) {
                                return;
                            }

                            if ($player->getCurrentClaim() === 'Spawn') {
                                return;
                            }

                            if (!isset($this->data[$player->getName()])) {
                                $this->data[$player->getName()] = 1;
                            }

                            $this->data[$player->getName()]++;

                            if($this->data[$player->getName()] < 3)return;
                                $player->sendMessage("§6You have activated§cThor");
                                $victim->getEffects()->add(new EffectInstance(VanillaEffects::BLINDNESS(), 20 * 8, false));
                                $victim->getEffects()->add(new EffectInstance(VanillaEffects::NAUSEA(), 20 * 8, false));
                                $victim->getEffects()->add(new EffectInstance(VanillaEffects::SLOWNESS(), 20 * 8, false));
                                $player->getEffects()->add(new EffectInstance(VanillaEffects::STRENGTH(), 20 * 8, false));
                                $player->getEffects()->add(new EffectInstance(VanillaEffects::SPEED(), 20 * 8, false));
                                $player->getEffects()->add(new EffectInstance(VanillaEffects::RESISTANCE(), 20 * 8, false));
                                Loader::getInstance()->getScheduler()->scheduleRepeatingTask(new ThorTask($victim), 20);
                                $player->getSession()->addCooldown('ability.thor', ' §l§7×§r &gThor§r§f: §f', 120);
                                $player->getSession()->addCooldown('ability.global', ' §l§7×§r &5Partner Item§r§f: §f', 5);
                                if($item->getCount() > 1){
                                    $item->setCount($item->getCount() - 1);
                                } else {
                                    $item = VanillaItems::AIR();
                                }
                                $player->getInventory()->setItemInHand($item);
                                unset($this->data[$player->getName()]);
                        } else {
                            $player->sendMessage("§6you have cooldown of §dPartner Item §f" . Timer::format($player->getSession()->getCooldown("ability.global")->getTime()));
                        }
                    } else {
                        $player->sendMessage("§cYou haven't Thor in the last " . Timer::format($player->getSession()->getCooldown("ability.thor")->getTime()));
                    }
                }
            }
        }
    }
}