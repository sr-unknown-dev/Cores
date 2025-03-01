<?php

namespace hcf\abilities\items;

use hcf\Loader;
use hcf\player\Player;
use hcf\utils\time\Timer;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\item\FishingRod;
use pocketmine\math\Vector3;
use pocketmine\utils\TextFormat;

class GrapplinHook implements Listener
{
    public function onItemUse(PlayerItemUseEvent $event): void
    {
        $item = $event->getItem();
        $player = $event->getPlayer();
        if ($player instanceof Player && $item instanceof FishingRod) {
            if ($item->getNamedTag()->getTag("Abilities") !== null && $item->getNamedTag()->getString("Abilities") === "GraphinHook") {
                if ($player->getSession()->getCooldown('ability.GraphinHook') === null) {
                    if ($player->getSession()->getCooldown('ability.global') === null) {
                        if ($player->getSession()->getCooldown('starting.timer') !== null || $player->getSession()->getCooldown('pvp.timer') !== null) {
                            return;
                        }

                        if ($player->getCurrentClaim() === 'Spawn') {
                            return;
                        }

                        $player->sendMessage("§6Has activado §9GraphinHook");
                        $player->getSession()->addCooldown('ability.GraphinHook', ' §l§7×§r §9GraphinHook§r§f: §f', 60);
                        $player->getSession()->addCooldown('ability.global', ' §l§7×§r &5Partner Item§r§f: §f', 5);

                        // Impulsar al jugador
                        $direction = $player->getDirectionVector();
                        $impulse = new Vector3($direction->getX() * 7, 1, $direction->getZ() * 7);
                        $player->setMotion($impulse);
                    } else {
                        $player->sendMessage("§c§6Tienes cooldown de §dPartner Item §f" . Timer::format($player->getSession()->getCooldown("ability.global")->getTime()));
                    }
                } else {
                    $player->sendMessage("§cNo puedes usar el §9GraphinHook§c por " . Timer::format($player->getSession()->getCooldown("ability.GraphinHook")->getTime()));
                }
            }
        }
    }
}