<?php

declare(strict_types=1);

namespace hcf\handler\crate\tile;

use hcf\entity\CustomItemEntity;
use hcf\entity\TextEntity;
use hcf\Loader;
use hcf\StaffMode\Vanish;
use hcf\Task\ItemDisplayTask;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;

use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\block\DyedShulkerBox as Shulker;
use pocketmine\block\Air;
use pocketmine\block\StainedGlass;
use pocketmine\block\tile\ShulkerBox;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\entity\Location;
use pocketmine\entity\object\ItemEntity;
use pocketmine\inventory\Inventory;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\VanillaItems;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\item\enchantment\EnchantmentInstance;

class CrateTile extends ShulkerBox
{

    /** @var string|null */
    private ?string $crateName;

    /** @var string|null */
    private ?string $text;

    /**
     * @return string|null
     */
    public function getCrateName(): ?string
    {
        return $this->crateName;
    }

    /**
     * @param string|null $crateName
     */
    public function setCrateName(?string $crateName): void
    {
        $this->crateName = $crateName;
        $this->createText();
    }

    /**
     * @param TextEntity $text
     */
    public function setText(TextEntity $text): void
    {
        $this->text = $text;
    }

    private function createText(): void
    {
        $crateName = $this->crateName;
        
        if ($crateName !== null) {
            $crate = Loader::getInstance()->getHandlerManager()->getCrateManager()->getCrate($crateName);
            $color = $crate->getColor();
            
            if ($crate !== null) {
                $nbt = $this->saveNBT();
                $textPos = $this->getPosition()->add(0.5, 1.4, 0.5);
                
                if (isset($crate->floatingTexts[$textPos->__toString()])) {
                    $crate->floatingTexts[$textPos->__toString()]->close();
                    unset($crate->floatingTexts[$textPos->__toString()]);
                }
                
                foreach ($this->getPosition()->getWorld()->getEntities() as $entity) {
                    if ($entity instanceof TextEntity) {
                        $entityPos = $entity->getPosition();
                        if ($entityPos->distance($textPos) < 0.5) {
                            $entity->close();
                        }
                    }
                }
                
                $crate->floatingTexts[$textPos->__toString()] = new TextEntity(new Location($textPos->getX(), $textPos->getY(), $textPos->getZ(), $this->getPosition()->getWorld(), 0.0, 0.0), $nbt);
                $crate->floatingTexts[$textPos->__toString()]->setNameTag(TextFormat::colorize(
                        $crate->getNameFormat()."\n" .
                        "\n§r" .
                        $color."Left§7 click to inspect crate.\n" .
                        $color."Right§7 click to open." . "\n" .
                        "\n§r" .
                        "§7§oTake a glance at our store!" . "\n" .
                        $color.Loader::getInstance()->getConfig()->get('tebex-crates')
                    ));
                $crate->floatingTexts[$textPos->__toString()]->spawnToAll();
            }
        }
    }

    public function close(): void
    {
        if ($this->crateName !== null) {
            $crate = Loader::getInstance()->getHandlerManager()->getCrateManager()->getCrate($this->crateName);
            if ($crate !== null) {
                $textPos = $this->getPosition()->add(0.5, 1.1, 0.5);
                if (isset($crate->floatingTexts[$textPos->__toString()])) {
                    $crate->floatingTexts[$textPos->__toString()]->close();
                    unset($crate->floatingTexts[$textPos->__toString()]);
                }
            }
        }
        parent::close();
    }

    /**
     * @param CompoundTag $nbt
     */
    protected function writeSaveData(CompoundTag $nbt) : void
    {
        parent::writeSaveData($nbt);
        $nbt->setString('crate_name', $this->getCrateName());
    }

    /**
     * @param CompoundTag $nbt
     */
    public function readSaveData(CompoundTag $nbt): void
    {
        parent::readSaveData($nbt);
        $this->crateName = $nbt->getString('crate_name');
        $this->createText();
    }

    /**
     * @param CompoundTag $nbt
     */
    public function addAdditionalSpawnData(CompoundTag $nbt): void
    {
        parent::addAdditionalSpawnData($nbt);
        $nbt->setString(self::TAG_ID, 'Shulker');
        $nbt->setByte('facing', 1);
    }

    /**
     * @param Player $player
     */
    public function openCratePreview(Player $player): void
    {
        if ($this->getCrateName() !== null) {
            $ItemNames = [];
            $crate = Loader::getInstance()->getHandlerManager()->getCrateManager()->getCrate($this->getCrateName());

            if ($crate !== null) {
                $menu = InvMenu::create(InvMenuTypeIds::TYPE_DOUBLE_CHEST);
                $crateItems = $crate->getItems();
                $contents = [];
                foreach ($crateItems as $slot => $item) {
                    $contents[$slot] = $item;
                }

                foreach ($crate->getItems() as $content) {
                    $name = trim($content->getName());
                    if ($name !== '') {
                        $ItemNames[] = $name;
                    }
                }
                $key = $crate->getKeyId();
                $key->setCustomName(TextFormat::colorize($crate->getKeyFormat()));
                $key->setCount(1);
                $key->setLore([
                    TextFormat::GRAY . 'You can redeem this key at crate',
                    TextFormat::GRAY . 'in the spawn area.',
                    '',
                    TextFormat::GRAY . TextFormat::ITALIC . 'Left click to view crate rewards.',
                    TextFormat::GRAY . TextFormat::ITALIC . 'Right click to open the crate.',
                ]);
                $content = VanillaItems::PAPER();
                $content->setCustomName(TextFormat::colorize($crate->getNameFormat()." Crate Content&f: \n"));
                $content->setCount(1);
                $content->setLore([implode(TextFormat::colorize(" \n&r"), $ItemNames)]);
                $tebex = VanillaItems::TOTEM();
                $tebex->setCustomName(TextFormat::colorize($crate->getColor()."Store: §f".Loader::getInstance()->getConfig()->get('tebex-crates')));
                $tebex->setCount(1);
                $menu->getInventory()->setContents($contents);
                $menu->getInventory()->setItem(50, $content);
                $menu->getInventory()->setItem(48, $tebex);
                $menu->getInventory()->setItem(49, $key);
                $menu->setListener(function (InvMenuTransaction $transaction) use($crate, $key): InvMenuTransactionResult {
                    $player = $transaction->getPlayer();
                    $item = $transaction->getItemClicked();
                    if($item->equals($key)){
                        foreach ($player->getInventory()->getContents() as $slot => $i) {
                            if ($i->getCustomName() === TextFormat::colorize($crate->getKeyFormat())) {
                                $i->pop();
                                $player->getInventory()->setItem($slot, $i);
                                $crate->giveReward($player);
                                break;
                            }
                        }
                        return $transaction->discard();
                    }
                    return $transaction->discard();
                });
                $crate = Loader::getInstance()->getHandlerManager()->getCrateManager()->getCrate($this->getCrateName());
                $menu->send($player, TextFormat::colorize($crate->getNameFormat() . ' &r&8Loot'));
            }
        }
    }

    /*public function showItemsAboveCrate(Player $player): void {
        $level = $this->getLevel();
        $entities = $level->getNearbyEntities($this->getBoundingBox()->expandedCopy(1, 1, 1), $player);
        $itemIds = [];

        foreach ($entities as $entity) {
            if ($entity instanceof Item) {
                $itemIds[] = $entity->getId();
            }
        }

        $player->sendMessage("Entidades arriba del crate: " . implode(", ", $itemIds));
    }*/

    /**
     * @param Player $player
     */
    public function openCrateConfiguration(Player $player): void
    {
        $menu = InvMenu::create(InvMenuTypeIds::TYPE_CHEST);
        $crate = Loader::getInstance()->getHandlerManager()->getCrateManager()->getCrate($this->getCrateName());

        $update_text = VanillaItems::FEATHER();
        $update_text->setCustomName(TextFormat::colorize('&eUpdate crate text'));
        $update_text->setNamedTag($update_text->getNamedTag()->setString('update_text', 'true'));

        $remove = VanillaItems::DIAMOND();
        $remove->setCustomName(TextFormat::colorize('&cRemove create tile'));
        $remove->setNamedTag($remove->getNamedTag()->setString('remove_tile', 'true'));

        $menu->getInventory()->setContents([
            12 => $update_text,
            14 => $remove
        ]);
        $menu->setListener(function (InvMenuTransaction $transaction): InvMenuTransactionResult {
            $item = $transaction->getItemClicked();
            $player = $transaction->getPlayer();

            if ($item->getNamedTag()->getTag('update_text') !== null) {
                if ($this->getCrateName() !== null) {
                    $crate = Loader::getInstance()->getHandlerManager()->getCrateManager()->getCrate($this->getCrateName());

                    if ($crate !== null) {
                        $textPos = $this->getPosition()->add(0.5, 1.1, 0.5);
                        if (isset($crate->floatingTexts[$textPos->__toString()])) {
                            $crate->floatingTexts[$textPos->__toString()]->setNameTag(TextFormat::colorize($crate->getNameFormat() . " Crate\n" . "\n&7Left click to view crate rewards.\n". "&7Right click to open the crate.\n\n&gghostly.tebex.io"));
                        }
                        $player->sendMessage(TextFormat::colorize('&aThe text of the crate ' . $this->getCrateName() . ' has been updated'));
                    } else $player->sendMessage(TextFormat::colorize('&cThere is no crate that is defined in the Tile'));
                }
            }

            if ($item->getNamedTag()->getTag('remove_tile') !== null) {
                $block = $this->getPosition()->getWorld()->getBlock($this->getPosition()->asVector3());
                $tile = $this->getPosition()->getWorld()->getTile($this->getPosition()->asVector3());

                if ($tile instanceof self)
                    $this->getPosition()->getWorld()->removeTile($tile);

                if ($block instanceof StainedGlass) $this->getPosition()->getWorld()->setBlock($this->getPosition()->asVector3(), VanillaBlocks::AIR());

                if  ($this->getCrateName() !== null) {
                    $crate = Loader::getInstance()->getHandlerManager()->getCrateManager()->getCrate($this->getCrateName());

                    if ($crate !== null) {
                        $textPos = $this->getPosition()->add(0.5, 1.1, 0.5);

                        if (isset($crate->floatingTexts[$textPos->__toString()])) {
                            $crate->floatingTexts[$textPos->__toString()]->close();
                            unset($crate->floatingTexts[$textPos->__toString()]);
                        }
                    }
                }
                $player->sendMessage(TextFormat::colorize('&cThe tile has been removed'));
            }
            return $transaction->discard();
        });
        $menu->send($player, TextFormat::colorize($crate->getNameFormat() . 'configuration'));
    }

    /**
     * @param Player $player
     */
    public function reedemKey(Player $player): void
    {
        if ($this->getCrateName() !== null) {
            $crate = Loader::getInstance()->getHandlerManager()->getCrateManager()->getCrate($this->getCrateName());

            if ($crate !== null) {
                $itemInHand = $player->getInventory()->getItemInHand();

                if ($itemInHand->hasNamedTag() && $itemInHand->getNamedTag()->getTag('crate_name') !== null) {
                    if ($itemInHand->getNamedTag()->getString('crate_name') === $this->getCrateName())
                        $crate->giveReward($player);
                }
            }
        }
    }
}
