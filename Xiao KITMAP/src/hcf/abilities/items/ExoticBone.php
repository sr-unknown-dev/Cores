<?php

namespace hcf\abilities\items;

use hcf\Loader;
use hcf\item\EnderpearlItem;
use hcf\player\Player;
use hcf\utils\Hits;
use hcf\utils\time\Timer;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\TextFormat;

class ExoticBone implements Listener
{

    public $data;

    public function onDamage(EntityDamageByEntityEvent $event): void
    {
        $victim = $event->getEntity();
        $player = $event->getDamager();
        if ($victim instanceof Player && $player instanceof Player) {
            $item = $player->getInventory()->getItemInHand();
            if ($item->getNamedTag()->getTag("Abilities") !== null) {
                if ($item->getNamedTag()->getString("Abilities") === "ExoticBone") {
                    if ($player->getSession()->getCooldown('ability.exoticbone') === null) {
                        if ($player->getSession()->getCooldown('ability.global') === null) {

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
                            
                            if (!isset($this->data[$player->getName()])) {
                                $this->data[$player->getName()] = 1;
                            }
                            $this->data[$player->getName()]++;
                            
                            if($this->data[$player->getName()] < 3)return;
                                $victim->getSession()->addCooldown('ability.exoticbonetag', '&aAntiTrapper Bone§r§f: §f', 20);
                                $player->sendMessage("§6You have activated §aAntiTrapper Bone");
                                $player->getSession()->addCooldown('ability.exoticbone', ' §l§7×§r &9Exotic Bone§r§f: §f', 120);
                                $player->getSession()->addCooldown('ability.global', ' §l§7×§r &5Partner Item§r§f: §f', 5);
                                unset($this->data[$player->getName()]);

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
                        $player->sendMessage("§cYou haven't AntiTrapper Bone in the last " . Timer::format($player->getSession()->getCooldown("ability.exoticbone")->getTime()));
                    }
                }
            }
        }
    }

    public function interactBlocks(PlayerInteractEvent $event)
    {
        $player = $event->getPlayer();
        if ($player instanceof Player) {
            if ($player->getSession()->getCooldown('ability.exoticbonetag') !== null) {
                $event->cancel();
            }
        }

    }

    public function breakBocks(BlockBreakEvent $event)
    {
        $player = $event->getPlayer();
        if ($player instanceof Player) {
            if ($player->getSession()->getCooldown('ability.exoticbonetag') !== null) {
                $event->cancel();
            }
        }

    }

    public function placeBlocks(BlockPlaceEvent $event)
    {
        $player = $event->getPlayer();
        if ($player instanceof Player) {
            if ($player->getSession()->getCooldown('ability.exoticbonetag') !== null) {
                $event->cancel();
            }
        }

    }
}