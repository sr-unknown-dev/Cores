<?php

namespace hcf\abilities\items;

use hcf\Loader;
use hcf\player\Player;
use hcf\utils\time\Timer;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\item\VanillaItems;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\TextFormat;

class StromBreaker implements Listener
{
    public $data;

    public function onDamage(EntityDamageByEntityEvent $event): void
    {
        $victim = $event->getEntity();
        $player = $event->getDamager();
        if ($victim instanceof Player && $player instanceof Player) {
            $item = $player->getInventory()->getItemInHand();
            if ($item->getNamedTag()->getTag("Abilities") !== null) {
                if ($item->getNamedTag()->getString("Abilities") === "Strombreaker") {
                    if ($player->getSession()->getCooldown('ability.strombreaker') === null) {
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

                            $helmet = $victim->getArmorInventory()->getHelmet();
                            if($helmet->isNull()){
                                $player->sendMessage("§cThe player does not have a helmet");
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
                            $player->sendMessage("§6You have activated §aStromBreaker");
                            $player->getSession()->addCooldown('ability.strombreaker', ' §l§7×§r &eStromBreaker§r§f: §f', 120);
                            $player->getSession()->addCooldown('ability.global', ' §l§7×§r &5Partner Item§r§f: §f', 5);
                            unset($this->data[$player->getName()]);


                            Loader::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($victim): void {

                                $helmet = $victim->getArmorInventory()->getHelmet();

                                if($helmet->isNull()){
                                    return;
                                }

                                $item = $victim->getInventory()->getItemInHand();
                                $victim->getArmorInventory()->setHelmet($item);
                                $victim->getInventory()->setItemInHand($helmet);

                            }), 60);

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
                        $player->sendMessage("§cYou haven't StromBreaker in the last " . Timer::format($player->getSession()->getCooldown("ability.strombreaker")->getTime()));
                    }
                }
            }
        }
    }
}