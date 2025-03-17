<?php

namespace hcf\abilities\items;

use hcf\abilities\entity\PortableBardEntity;
use hcf\Loader;
use hcf\item\EnderpearlItem;
use hcf\player\Player;
use hcf\utils\time\Timer;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\item\VanillaItems;
use pocketmine\utils\TextFormat;
use pocketmine\world\Position;

class PortableBard implements Listener
{

    public function interactPortable(PlayerInteractEvent $event)
    {
        $item = $event->getItem();
        $player = $event->getPlayer();
        if ($player instanceof Player)
        if ($item->getNamedTag()->getTag("Abilities") !== null) {
            if ($item->getNamedTag()->getString("Abilities") === "PortableBard") {
                $event->cancel();
            }
        }
    }
    public function onItemUse(PlayerItemUseEvent $event): void
    {
        $item = $event->getItem();
        $player = $event->getPlayer();
        if ($player instanceof Player)
            if ($item->getNamedTag()->getTag("Abilities") !== null) {
                if ($item->getNamedTag()->getString("Abilities") === "PortableBard") {
                    $event->cancel();
                    if ($player->getSession()->getCooldown('ability.portablebard') === null) {
                        if ($player->getSession()->getCooldown('ability.global') === null) {
                            if ($player->getSession()->getCooldown('starting.timer') !== null || $player->getSession()->getCooldown('pvp.timer') !== null) {
                                return;
                            }

                            if ($player->getCurrentClaim() === 'Spawn') {
                                return;
                            }

                            if ($player->getSession()->getFaction() !== null && $player->getSession()->getFaction() !== null) {
                                if ($player->getSession()->getFaction() === $player->getSession()->getFaction()) {
                                    $bard = new PortableBardEntity($event->getPlayer()->getLocation());
                                    $bard->setOwner($event->getPlayer());
                                    $bard->setPos($event->getPlayer()->getLocation());
                                    $bard->spawnToAll();
                                    Loader::$bard_allow[$event->getPlayer()->getName()] = true;
                                    $player->sendMessage("§6Has activado §dPortable Bard");
                                    
                                    $item = $player->getInventory()->getItemInHand();
                                    if ($item->getCount() > 1) {
                                        $item->setCount($item->getCount() - 1);
                                        $player->getInventory()->setItemInHand($item);
                                    } else {
                                        $player->getInventory()->setItemInHand(VanillaItems::AIR());
                                    }
                                    
                                    return;
                                }
                            }
                            $player->getSession()->addCooldown('ability.portablebard', ' §l§7×§r &dPortable Bard§r§f: §f', 240);
                            $player->getSession()->addCooldown('ability.global', ' §l§7×§r &5Partner Item§r§f: §f', 5);

                            $item = $player->getInventory()->getItemInHand();
                            if ($item->getCount() > 1) {
                                $item->setCount($item->getCount() - 1);
                                $player->getInventory()->setItemInHand($item);
                            } else {
                                $player->getInventory()->setItemInHand(VanillaItems::AIR());
                            }

                        } else {
                            $player->sendMessage("§6Tienes cooldown de §dPartner Item §f" . Timer::format($player->getSession()->getCooldown("ability.global")->getTime()));
                        }
                    } else {
                        $player->sendMessage("§cNo tienes Portable Bard en los últimos " . Timer::format($player->getSession()->getCooldown("ability.portablebard")->getTime()));
                    }
                }
            }
    }

    public function Interact(PlayerInteractEvent $event): void
    {
        $item = $event->getItem();
        $player = $event->getPlayer();

        if ($item->getNamedTag()->getTag("Abilities") !== null) {
            if ($item->getNamedTag()->getString("Abilities") === "PortableBard") {
                $event->cancel();
            }
        }
    }
    
    public static function isAllow(Player $player): bool
    {
        if(!isset(Loader::$bard_allow[$player->getName()])){
            return false;
        }
        return true;
    }

    public static function setAllow(Player $player){
        Loader::$bard_allow[$player->getName()] = true;
    }

    public static function removeAllow(Player $player){
        unset(Loader::$bard_allow[$player->getName()]);
    }
}