<?php

namespace hcf\abilities\items;

use hcf\Loader;
use hcf\player\Player;
use hcf\utils\time\Timer;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemUseEvent;

class Firework implements Listener
{

    /**
     * Pete Zenji
     *
     * @param PlayerItemUseEvent $event
     * @return void
     */
    public function onItemUse(PlayerItemUseEvent $event): void
    {
        $item = $event->getItem();
        $player = $event->getPlayer();
        if ($player instanceof Player)
            if ($item->getNamedTag()->getTag("Abilities") !== null) {
                if ($item->getNamedTag()->getString("Abilities") === "Firework") {
                    $event->cancel();
                    if ($player->getSession()->getCooldown('ability.Firework') === null) {
                        if ($player->getSession()->getCooldown('ability.global') === null) {
                            if ($player->getSession()->getCooldown('starting.timer') !== null || $player->getSession()->getCooldown('pvp.timer') !== null) {
                                return;
                            }

                            if ($player->getCurrentClaim() === 'Spawn') {
                                return;
                            }
                            $player->knockBack($player->getDirectionVector()->x, $player->getDirectionVector()->z, 2, 2, false);
                            $player->sendMessage("§6You have activated §3Firework");
                            $player->getSession()->addCooldown('ability.Firework', ' §l§7×§r §3Firework§r§f: §f', 45);
                            $player->getSession()->addCooldown('ability.global', ' §l§7×§r &5Partner Item§r§f: §f', 5);
                            $item = $event->getItem();
                            $item->pop();
                            $player->getInventory()->setItemInHand($item);

                        } else {
                            $player->sendMessage("§6you have cooldown of §dPartner Item §f" . Timer::format($player->getSession()->getCooldown("ability.global")->getTime()));
                        }
                    } else {
                        $player->sendMessage("§cYou haven't FireWork in the last " . Timer::format($player->getSession()->getCooldown("ability.Firework")->getTime()));
                    }
                }
            }
    }

    public function interactFirework(PlayerInteractEvent $event)
    {
        $player = $event->getPlayer();
        $item = $player->getInventory()->getItemInHand();
        if ($item->getNamedTag()->getTag("Abilities") !== null) {
            if ($item->getNamedTag()->getString("Abilities") === "Firework") {
                $event->cancel();
            }
        }
    }

    public function handleFall(EntityDamageEvent $event) {
        $player = $event->getEntity();
        if($event->getCause() === EntityDamageEvent::CAUSE_FALL) {
            if ($player instanceof Player) {
                if ($player->getSession()->getCooldown('ability.Firework') !== null) {
                    $event->cancel();
                }
            }
        }
    }
}