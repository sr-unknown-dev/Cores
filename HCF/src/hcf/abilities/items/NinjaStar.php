<?php

namespace hcf\abilities\items;

use hcf\Loader;
use hcf\item\EnderpearlItem;
use hcf\player\Player;
use hcf\utils\time\Timer;
use hcf\utils\Utils;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\types\LevelSoundEvent;
use pocketmine\scheduler\ClosureTask;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class NinjaStar implements Listener
{

    public function onItemUse(PlayerItemUseEvent $event): void
    {
        $item = $event->getItem();
        $player = $event->getPlayer();
        if ($player instanceof Player)
            if ($item->getNamedTag()->getTag("Abilities") !== null) {
                if ($item->getNamedTag()->getString("Abilities") === "NinjaStar") {
                    $event->cancel();
                    if ($player->getSession()->getCooldown('ability.Ninjastar') === null) {
                        if ($player->getSession()->getCooldown('ability.global') === null) {
                            if ($player->getSession()->getCooldown('starting.timer') !== null || $player->getSession()->getCooldown('pvp.timer') !== null) {
                                return;
                            }

                            if ($player->getCurrentClaim() === 'Spawn') {
                                return;
                            }

                            if ($player->getSession()->getCooldown('lastDamage') === null) {
                                $player->sendMessage(TextFormat::colorize("&cNo one has attacked you in the past 15 seconds!"));
                                return;
                            }

                            $cause = $player->getLastDamageCause();

                            
                            
                            if($cause === null) {
                                $player->sendMessage(TextFormat::colorize("&cNo one has attacked you in the past 15 seconds!"));
                            }
                            if (!$cause instanceof EntityDamageByEntityEvent) {
                                $player->sendMessage(TextFormat::colorize("&cNo one has attacked you in the past 15 seconds!"));
                                return;
                            }
                            $damager = $cause->getDamager();
                            if (!$damager instanceof Player) {
                                $player->sendMessage(TextFormat::colorize("&cNo one has attacked you in the past 15 seconds!"));
                            }

                            $player->sendMessage("§6You have activated §bNinja Star");
                            Utils::PlaySound($player, "random.orb", 1, 1);
                            Loader::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($damager, $player): void {
                                $player->sendMessage(TextFormat::colorize("&gTeleporting in &65&cseconds..."));

                            }), 20 * 1);
                            Loader::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($damager, $player): void {
                                $player->sendMessage(TextFormat::colorize("&gTeleporting in &64&cseconds..."));

                            }), 20 * 2);
                            Loader::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($damager, $player): void {
                                $player->sendMessage(TextFormat::colorize("&gTeleporting in &63&cseconds..."));

                            }), 20 * 3);
                            Loader::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($damager, $player): void {
                                $player->sendMessage(TextFormat::colorize("&gTeleporting in &62&cseconds..."));

                            }), 20 * 4);
                            Loader::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($damager, $player): void {
                                $player->sendMessage(TextFormat::colorize("&gTeleporting in &61&cseconds..."));

                            }), 20 * 5);
                            Loader::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($damager, $player): void {
                                $player->sendMessage(TextFormat::colorize("&gTeleporting in &60&cseconds..."));

                            }), 20 * 6);
                            Loader::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($player, $damager): void {
                                if (!$player === null) {
                                    $player->teleport($damager->getPosition());
                                    $player->getSession()->addCooldown('ability.Ninjastar', ' §l§7×§r §bNinja Star§r§f: §f', 120);
                                    $player->getSession()->addCooldown('ability.global', ' §l§7×§r &5Partner Item§r§f: §f', 5);

                                    if ($damager instanceof Player && $player instanceof Player){
                                        Utils::PlaySound($player, "random.anvil_use", 1, 1);
                                        $player->sendMessage(TextFormat::colorize("&7You've been &dteleported&7 to&f {$damager->getName()}."));
                                        $damager->sendMessage(TextFormat::colorize("&f{$player->getName()} &7has been &dteleported &7to you."));
                                    };

                                    foreach (Server::getInstance()->getOnlinePlayers() as $players){
                                        $players->getNetworkSession()->sendDataPacket(Utils::addParticle($damager->getPosition(), "minecraft:end_chest"));
                                    }
                                }else {
                                    return;
                                }
                            }), 20 * 7);
                                
                            $item = $event->getItem();
                            $item->pop();
                            $player->getInventory()->setItemInHand($item);

                        } else {
                            $player->sendMessage("§6you have cooldown of §dPartner Item §f" . Timer::format($player->getSession()->getCooldown("ability.global")->getTime()));
                        }
                    } else {
                        $player->sendMessage("§cYou haven't Ninja Star in the last " . Timer::format($player->getSession()->getCooldown("ability.Ninjastar")->getTime()));
                    }
                }
            }
    }
}

