<?php

namespace hcf\abilities\items;

use hcf\Loader;
use hcf\player\Player;
use hcf\utils\time\Timer;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat;

class FocusMode implements Listener
{
    private array $focus = [];
    private array $data = [];

    public function onDamage(EntityDamageByEntityEvent $event): void
    {
        $damager = $event->getDamager();
        
        if ($damager instanceof Player) {
            $item = $damager->getInventory()->getItemInHand();
            
            if ($item->getNamedTag()->getTag("Abilities") !== null) {
                if ($item->getNamedTag()->getString("Abilities") === "FocusMode") {
                    $event->cancel();
                    if ($damager->getSession()->getCooldown('ability.FocusMode') === null) {
                        if ($damager->getSession()->getCooldown('ability.global') === null) {
                            if ($damager->getSession()->getCooldown('starting.timer') !== null || $damager->getSession()->getCooldown('pvp.timer') !== null) {
                                return;
                            }

                            if ($damager->getCurrentClaim() === 'Spawn') {
                                return;
                            }

                            $damager->getEffects()->add(new EffectInstance(VanillaEffects::SPEED(), 20 * 8, 2));
                            $damager->sendMessage("§6Has activado§cFocus Mode");
                            $damager->sendMessage(TextFormat::colorize("&cCooldown activo de &gFocus Mode &r&cpor 2 minutos"));
                            $damager->getSession()->addCooldown('ability.FocusMode', ' §l§7×§r§cFocus Mode§r§f: §f', 120);
                            $damager->getSession()->addCooldown('ability.global', ' §l§7×§r &5Partner Item§r§f: §f', 5);
                            $item = $damager->getInventory()->getItemInHand();
                            $item->pop();
                            $damager->getInventory()->setItemInHand($item);
                            $damager->sendMessage("§4♥ §cHas activado §6Focus Mode§r§c, harás un 40% más de daño por 10 segundos");
                            $damagerName = $damager->getName();
                            $this->focus[$damagerName] = true;
                        } else {
                            $damager->sendMessage("§c§6Tienes cooldown de §dPartner Item §f" . Timer::format($damager->getSession()->getCooldown("ability.global")->getTime()));
                        }
                    } else {
                        $damager->sendMessage("§cNo puedes usar el§cFocusMode§c por " . Timer::format($damager->getSession()->getCooldown("ability.FocusMode")->getTime()));
                    }
                }
            }
        }
    }

    public function onEntity(EntityDamageEvent $event): void
    {
        if ($event instanceof EntityDamageByEntityEvent) {
            $damager = $event->getDamager();
            if ($damager instanceof Player) {
                $damagerName = $damager->getName();

                if (isset($this->focus[$damagerName]) && $this->focus[$damagerName] === true) {
                    if (!isset($this->data[$damagerName])) {
                        $this->data[$damagerName] = 1;
                    }

                    $this->data[$damagerName]++;

                    if($this->data[$damagerName] >= 3){
                        $event->setBaseDamage($event->getBaseDamage() + ($event->getBaseDamage() * 0.4));
                        unset($this->data[$damagerName]);
                    }
                } else {
                    unset($this->data[$damagerName]);
                }
            }
        }
    }
}
