<?php

namespace hcf\abilities\items;

use hcf\abilities\entity\FrezzerGunEntity;
use hcf\abilities\entity\SwitcherEntity;
use hcf\Loader;
use hcf\item\EnderpearlItem;
use hcf\player\Player;
use hcf\utils\time\Timer;
use pocketmine\entity\Location;
use pocketmine\event\entity\ProjectileHitEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\TextFormat;
use pocketmine\world\Position;

class Switcher implements Listener
{
    public function onItemUse(PlayerItemUseEvent $event): void
    {
        $item = $event->getItem();
        $player = $event->getPlayer();
        if ($player instanceof Player)
            if ($item->getNamedTag()->getTag("Abilities") !== null) {
                if ($item->getNamedTag()->getString("Abilities") === "Switcher") {
                    $event->cancel();
                    if ($player->getSession()->getCooldown('ability.Switcher') === null) {
                        if ($player->getSession()->getCooldown('ability.global') === null) {
                            if ($player->getSession()->getCooldown('starting.timer') !== null || $player->getSession()->getCooldown('pvp.timer') !== null) {
                                return;
                            }

                            if ($player->getCurrentClaim() === 'Spawn') {
                                return;
                            }
                            $entity = new SwitcherEntity(Location::fromObject($player->getEyePos(), $player->getWorld(), $player->getLocation()->getYaw(), $player->getLocation()->getPitch()), $player);
                            $entity->setMotion($event->getDirectionVector()->multiply(1.5));
                            $entity->spawnToAll();
                            $player->sendMessage("§6You have activated §aSwitcher");
                            $player->getSession()->addCooldown('ability.Switcher', ' §l§7×§r §aSwitcher§r§f: §f', 20);
                            $player->getSession()->addCooldown('ability.global', ' §l§7×§r &5Partner Item§r§f: §f', 5);
                            $item = $event->getItem();
                            $item->pop();
                            $player->getInventory()->setItemInHand($item);

                        } else {
                            $player->sendMessage("§6you have cooldown of §dPartner Item §f" . Timer::format($player->getSession()->getCooldown("ability.global")->getTime()));
                        }
                    } else {
                        $player->sendMessage("§cNo puedes usar el §aSwitcher§c por " . Timer::format($player->getSession()->getCooldown("ability.Switcher")->getTime()));
                    }
                }
            }
    }

    public function onHitByProjectile(ProjectileHitEntityEvent $event) : void
    {
        $hit = $event->getEntityHit();
        if ($hit instanceof Player) {
            $entity = $event->getEntity();
            $player = $entity->getOwningEntity();
            if ($player instanceof Player) {
                if ($entity instanceof SwitcherEntity) {
                    if ($player->getSession()->getCooldown('starting.timer') !== null || $player->getSession()->getCooldown('pvp.timer') !== null) {
                        return;
                    }

                    if ($player->getCurrentClaim() === 'Spawn') {
                        return;
                    }
                    if ($hit->getSession()->getCooldown('starting.timer') !== null || $hit->getSession()->getCooldown('pvp.timer') !== null) {
                        return;
                    }

                    if ($hit->getCurrentClaim() === 'Spawn') {
                        return;
                    }
                    $pos1 = $player->getPosition();
                    $pos2 = $hit->getPosition();
                    if ($pos1 instanceof Position)
                    $hit->teleport($pos1);
                    self::playSound($pos1, "mob.endermite.hit");
                    self::playSound($pos2, "mob.endermite.hit");
                    $player->teleport($pos2);
                }
            }
        }
    }

    protected static function playSound(Position $pos, string $soundName):void {
        $sPk = new PlaySoundPacket();
        $sPk->soundName = $soundName;
        $sPk->x = $pos->x;
        $sPk->y = $pos->y;
        $sPk->z = $pos->z;
        $sPk->volume = $sPk->pitch = 1;
        $pos->getWorld()->broadcastPacketToViewers($pos, $sPk);
    }
}