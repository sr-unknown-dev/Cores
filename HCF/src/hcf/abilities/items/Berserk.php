<?php

namespace hcf\abilities\items;

use hcf\player\Player;
use hcf\utils\time\Timer;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;

class Berserk implements Listener
{
    public function onItemUse(PlayerItemUseEvent $event): void
    {
        $item = $event->getItem();
        $player = $event->getPlayer();
        if ($player instanceof Player)
            if ($item->getNamedTag()->getTag("Abilities") !== null) {
                if ($item->getNamedTag()->getString("Abilities") === "Berserk") {
                    $event->cancel();
                    if ($player->getSession()->getCooldown('ability.Berserk') === null) {
                        if ($player->getSession()->getCooldown('ability.global') === null) {
                            if ($player->getSession()->getCooldown('starting.timer') !== null || $player->getSession()->getCooldown('pvp.timer') !== null) {
                                return;
                            }

                            if ($player->getCurrentClaim() === 'Spawn') {
                                return;
                            }
                            $player->getEffects()->add(new EffectInstance(VanillaEffects::STRENGTH(), 13 * 20, 1));
                            $player->getEffects()->add(new EffectInstance(VanillaEffects::RESISTANCE(), 13 * 20, 2));
                            $player->getEffects()->add(new EffectInstance(VanillaEffects::SPEED(), 13 * 20, 2));
                            $player->sendMessage("§6You have activated §cBerserk");
                            $player->getSession()->addCooldown('ability.Berserk', ' §l§7×§r §cBerserk§r§f: §f', 45);
                            $player->getSession()->addCooldown('ability.global', ' §l§7×§r &5Partner Item§r§f: §f', 5);
                            $item = $event->getItem();
                            $item->pop();
                            $player->getInventory()->setItemInHand($item);

                        } else {
                            $player->sendMessage("§6you have cooldown of §dPartner Item §f" . Timer::format($player->getSession()->getCooldown("ability.global")->getTime()));
                        }
                    } else {
                        $player->sendMessage("§cYou haven't Berserk in the last " . Timer::format($player->getSession()->getCooldown("ability.Berserk")->getTime()));
                    }
                }
            }
    }
}