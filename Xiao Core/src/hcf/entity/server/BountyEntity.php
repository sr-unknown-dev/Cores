<?php

declare(strict_types=1);

namespace hcf\entity\server;

use hcf\command\moderador\TopKDREntity;
use hcf\Loader;
use hcf\utils\inventorie\Inventories;
use Himbeer\LibSkin\SkinConverter;
use itoozh\bounties\session\SessionFactory;
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
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class BountyEntity extends Human
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
        $text = TextFormat::colorize("--------------------\n&bBounty\n--------------------");

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
            }
            SessionFactory::getInstance()->get($damager)->openMenu($damager);
        }

    }
}