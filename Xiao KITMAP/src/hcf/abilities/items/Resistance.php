<?php

namespace hcf\abilities\items;

use hcf\player\Player;
use hcf\utils\time\Timer;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;

class Resistance implements Listener
{
    public function onItemUse(PlayerItemUseEvent $event): void
    {
        $item = $event->getItem();
        $player = $event->getPlayer();
        if ($player instanceof Player)
            if ($item->getNamedTag()->getTag("Abilities") !== null) {
                if ($item->getNamedTag()->getString("Abilities") === "Resistance") {
                    $event->cancel();
                    if ($player->getSession()->getCooldown('ability.Resistance') === null) {
                        if ($player->getSession()->getCooldown('ability.global') === null) {
                            if ($player->getSession()->getCooldown('starting.timer') !== null || $player->getSession()->getCooldown('pvp.timer') !== null) {
                                return;
                            }

                            if ($player->getCurrentClaim() === 'Spawn') {
                                return;
                            }
                            $player->getEffects()->add(new EffectInstance(VanillaEffects::RESISTANCE(), 20 * 8, 2));
                            $player->sendMessage("§6You have activated §6Resistance");
                            $player->getSession()->addCooldown('ability.Resistance', ' §l§7×§r §6Resistance§r§f: §f', 45);
                            $player->getSession()->addCooldown('ability.global', ' §l§7×§r &5Partner Item§r§f: §f', 5);
                            $item = $event->getItem();
                            $item->pop();
                            $player->getInventory()->setItemInHand($item);

                        } else {
                            $player->sendMessage("§6you have cooldown of §dPartner Item §f" . Timer::format($player->getSession()->getCooldown("ability.global")->getTime()));
                        }
                    } else {
                        $player->sendMessage("§cNo puedes usar el §6Resistance§c por " . Timer::format($player->getSession()->getCooldown("ability.Resistance")->getTime()));
                    }
                }
            }
    }
}