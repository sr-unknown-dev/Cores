<?php

namespace hcf\abilities\items;

use hcf\Loader;
use hcf\item\EnderpearlItem;
use hcf\player\Player;
use hcf\utils\time\Timer;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use pocketmine\scheduler\ClosureTask;

class SecondChance implements Listener
{
    public function onItemUse(PlayerItemUseEvent $event): void
    {
        $item = $event->getItem();
        $player = $event->getPlayer();
        if ($player instanceof Player)
            if ($item->getNamedTag()->getTag("Abilities") !== null) {
                if ($item->getNamedTag()->getString("Abilities") === "SecondChance") {
                    $event->cancel();
                    if ($player->getSession()->getCooldown('ability.secondchance') === null) {
                        if ($player->getSession()->getCooldown('ability.global') === null) {
                            if ($player->getSession()->getCooldown('enderpearl') === null) {
                                $player->sendMessage("§cNo tienes Cooldown de EnderPearl");
                                return;
                            }
                            if ($player->getSession()->getCooldown('starting.timer') !== null || $player->getSession()->getCooldown('pvp.timer') !== null) {
                                return;
                            }

                            if ($player->getCurrentClaim() === 'Spawn') {
                                return;
                            }
                            $player->getSession()->removeCooldown('enderpearl');
                            $player->sendMessage("§6You have activated §3SecondChance");
                            $player->getSession()->addCooldown('ability.secondchance', ' §l§7×§r &3SecondChance§r§f: §f', 120);
                            $player->getSession()->addCooldown('ability.global', ' §l§7×§r &5Partner Item§r§f: §f', 5);
                            $item = $event->getItem();
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
                        $player->sendMessage("§cNo puedes usar el §3SecondChance§c por " . Timer::format($player->getSession()->getCooldown("ability.secondchance")->getTime()));
                    }
                }
            }
    }
}