<?php

namespace hcf\abilities\items;

use hcf\Loader;
use hcf\item\EnderpearlItem;
use hcf\player\Player;
use hcf\utils\time\Timer;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\math\Vector3;
use pocketmine\scheduler\ClosureTask;

class TimeWarp implements Listener
{
    public function onItemUse(PlayerItemUseEvent $event): void
    {
        $item = $event->getItem();
        $player = $event->getPlayer();
        if ($player instanceof Player)
            if ($item->getNamedTag()->getTag("Abilities") !== null) {
                if ($item->getNamedTag()->getString("Abilities") === "TimeWarp") {
                    $event->cancel();
                    if ($player->getSession()->getCooldown('ability.timewarp') === null) {
                        if ($player->getSession()->getCooldown('ability.global') === null) {
                            $position = EnderpearlItem::getLastHit($player);
                            if ($player->getSession()->getCooldown('starting.timer') !== null || $player->getSession()->getCooldown('pvp.timer') !== null) {
                                return;
                            }

                            if ($player->getCurrentClaim() === 'Spawn') {
                                return;
                            }

                                Loader::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($player, $position): void {
                                    if ($player->isOnline()) {
                                        $player->teleport($position);
                                    }
                                }), 20 * 5);
                                $player->sendMessage(" ");
                                $player->sendMessage("§c* §7Acabas de usar el §6TimeWarp");
                                $player->sendMessage("§c* §7En 5 segundos seras teletransportado ");
                                 $player->sendMessage("§c* §7A la posicion de tu ultima EnderPearl.");
                                $player->sendMessage(" ");
                                $player->getSession()->addCooldown('ability.timewarp', ' §l§7×§r &6Time Warp§r§f: §f', 10);
                                $player->getSession()->addCooldown('ability.global', ' §l§7×§r &5Partner Item§r§f: §f', 5);

                        } else {
                            $player->sendMessage("§6you have cooldown of §dPartner Item §f" . Timer::format($player->getSession()->getCooldown("ability.global")->getTime()));
                        }
                    } else {
                        $player->sendMessage("§cNo puedes usar el §6TimeWarp§c por " . Timer::format($player->getSession()->getCooldown("ability.timewarp")->getTime()));
                    }
                }
            }
    }
}