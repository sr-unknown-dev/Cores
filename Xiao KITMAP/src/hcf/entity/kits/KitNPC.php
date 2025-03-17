<?php

declare(strict_types=1);

namespace hcf\entity\kits;

use hcf\Loader;
use Himbeer\LibSkin\SkinConverter;
use pocketmine\entity\Human;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\VanillaItems;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class KitNPC extends Human
{
    private string $kitName = "";
    private string $displayText = "";
    public bool $canCollide = false;
    protected bool $immobile = true;

    protected function getInitialDragMultiplier(): float
    {
        return 0.00;
    }

    protected function getInitialGravity(): float
    {
        return 0.00;
    }

    public static function create(Player $player, string $displayText, string $kitName): self
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
            ]))
            ->setString("KitName", $kitName)
            ->setString("DisplayText", $displayText);

        $npc = new self($player->getLocation(), $player->getSkin(), $nbt);
        $npc->setKitName($kitName);
        $npc->setDisplayText($displayText);

        $kit = Loader::getInstance()->getHandlerManager()->getKitManager()->getKit($kitName)
            ?? Loader::getInstance()->getHandlerManager()->getKitPayManager()->getKit($kitName)
            ?? Loader::getInstance()->getHandlerManager()->getKitOpManager()->getKit($kitName);

        if ($kit !== null) {
            $armorItems = $kit->getArmor();
            if (isset($armorItems[0])) {
                $npc->getArmorInventory()->setHelmet($armorItems[0]);
            }
            if (isset($armorItems[1])) {
                $npc->getArmorInventory()->setChestplate($armorItems[1]);
            }
            if (isset($armorItems[2])) {
                $npc->getArmorInventory()->setLeggings($armorItems[2]);
            }
            if (isset($armorItems[3])) {
                $npc->getArmorInventory()->setBoots($armorItems[3]);
            }
        }

        return $npc;
    }

    public function canBeMovedByCurrents(): bool
    {
        return false;
    }

    protected function initEntity(CompoundTag $nbt): void
    {
        parent::initEntity($nbt);
        if ($nbt->getTag("KitName") !== null) {
            $this->kitName = $nbt->getString("KitName");
        }
        if ($nbt->getTag("DisplayText") !== null) {
            $this->displayText = $nbt->getString("DisplayText");
        }
    }

    public function saveNBT(): CompoundTag
    {
        $nbt = parent::saveNBT();
        $nbt->setString("KitName", $this->kitName);
        $nbt->setString("DisplayText", $this->displayText);
        return $nbt;
    }

    public function setKitName(string $kitName): void
    {
        $this->kitName = $kitName;
    }

    public function setDisplayText(string $text): void
    {
        $this->displayText = $text;
    }

    public function onUpdate(int $currentTick): bool
    {
        $this->setNameTagAlwaysVisible();
        $this->setNameTag(TextFormat::colorize($this->displayText));
        return parent::onUpdate($currentTick);
    }

    public function attack(EntityDamageEvent $source): void
    {
        $source->cancel();

        if ($source instanceof EntityDamageByEntityEvent) {
            $damager = $source->getDamager();

            if ($damager instanceof Player) {
                if ($damager->hasPermission('npc.command') && $damager->getInventory()->getItemInHand()->getCustomName() === "§eRemove NPC §r§7(Right Click)" && $damager->getInventory()->getItemInHand() === VanillaItems::GOLDEN_HOE()) {
                    $this->kill();
                    return;
                }

                $kit = Loader::getInstance()->getHandlerManager()->getKitManager()->getKit($this->kitName)
                    ?? Loader::getInstance()->getHandlerManager()->getKitPayManager()->getKit($this->kitName)
                    ?? Loader::getInstance()->getHandlerManager()->getKitOpManager()->getKit($this->kitName);

                if ($kit !== null) {
                    $kit->giveTo($damager);
                    $damager->sendMessage("§aYou received the " . $this->kitName . " kit!");
                } else {
                    $damager->sendMessage("§cThis kit does not exist!");
                }
            }
        }
    }
}