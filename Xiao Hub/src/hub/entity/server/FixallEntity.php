<?php

declare(strict_types=1);

namespace hub\entity\server;

use hub\command\moderador\TopKDREntity;
use hub\Loader;
use hub\player\Player;
use hub\utils\inventorie\Inventories;
use Himbeer\LibSkin\SkinConverter;
use JetBrains\PhpStorm\Pure;
use pocketmine\entity\Human;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\Armor;
use pocketmine\item\Durable;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\utils\TextFormat;

class FixallEntity extends Human
{

    public bool $canCollide = false;
    protected bool $immobile = true;

    protected function getInitialDragMultiplier() : float{ return 0.00; }

    protected function getInitialGravity() : float{ return 0.00; }

    /** @var int|null */

    /**
     * @param Player $player
     *
     * @return TopKDREntity
     */
    public static function create(Player $player): self
    {
        $nbt = CompoundTag::create()
            ->setTag("Pos", new ListTag([
                new DoubleTag($player->getLocation()->x),
                new DoubleTag($player->getLocation()->y),
                new DoubleTag($player->getLocation()->z)
            ]))
            ->setTag("Motion", new ListTag([
                new DoubleTag($player->getMotion()->x),
                new DoubleTag($player->getMotion()->y),
                new DoubleTag($player->getMotion()->z)
            ]))
            ->setTag("Rotation", new ListTag([
                new FloatTag($player->getLocation()->yaw),
                new FloatTag($player->getLocation()->pitch)
            ]));
        return new self($player->getLocation(), $player->getSkin(), $nbt);
    }

    public function canBeMovedByCurrents(): bool
    {
        return false;
    }

    /**
     * @param int $currentTick
     *
     * @return bool
     * @throws \Exception
     */
    public function onUpdate(int $currentTick): bool
    {
        $text = TextFormat::colorize("--------------------\n&9Fix All\n--------------------");

        $this->setNameTagAlwaysVisible();
        $this->setNameTag($text);
        return parent::onUpdate($currentTick);
    }

    /**
     * @param EntityDamageEvent $source
     */
    public function attack(EntityDamageEvent $source): void {

        $source->cancel();

        if ($source instanceof EntityDamageByEntityEvent) {
            $damager = $source->getDamager();

            if ($damager instanceof Player) {
                if ($damager->hasPermission('npc.command') && $damager->getInventory()->getItemInHand()->getCustomName() === "§eRemove NPC §r§7(Right Click)") {
                    $this->kill();
                    return;
                }

                if ($damager->hasPermission('fix.command')) {
                    $this->Fixall($damager);
                    $damager->sendMessage(TextFormat::colorize('&aSomeone fixed your items and armor successfully'));
                }elseif ($damager->getSession()->getBalance() >= 20000) {
                    $this->Fixall($damager);
                    $balance = $damager->getSession()->getBalance();
                    $restbalance = $balance - 20000;
                    $damager->getSession()->setBalance($restbalance);
                    $damager->sendMessage(TextFormat::colorize('&aSomeone fixed your items and armor successfully'));
                }elseif ($damager->getSession()->getBalance() < 20000) {
                    $damager->sendMessage(TextFormat::RED."You don't have enough money");
                }
            }
        }

    }

    public function Fixall(Player $player){
        foreach ($player->getInventory()->getContents() as $slot => $item) {
            if ($item instanceof Durable && $item->getDamage() > 0) {
                $newItem = $item->setDamage(0);
                $player->getInventory()->setItem($slot, $newItem);
            }
        }

        foreach ($player->getArmorInventory()->getContents() as $slot => $armor) {
            if(!$armor instanceof Armor) return;
            if ($armor->getDamage() > 0) {
                $newArmor = $armor->setDamage(0); $player->getArmorInventory()->setItem($slot, $newArmor);
            }
        }
    }
}