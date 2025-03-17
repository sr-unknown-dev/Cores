<?php

namespace hcf\handler\lootbox;

use hcf\Loader;
use hcf\player\Player;
use hcf\utils\messages\Messages;
use hcf\utils\serialize\Serialize;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\block\VanillaBlocks;
use pocketmine\inventory\Inventory;
use pocketmine\item\VanillaItems;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

class Lootbox
{
    private $items;
    public Config $config;

    public function __construct()
    {
        $this->config = new Config(Loader::getInstance() . "lootbox.json", Config::JSON);
    }

    /**
     * @param mixed $items
     */
    public function setItems(array $items): void
    {
        $content = [];
        foreach ($items as $item) {
            $content[] = Serialize::serialize($item);
        }

        if (empty($content)) return;

        $this->config->set("items", $content);
        $this->config->save();
    }

    /**
     * @return array
     */
    public function getItems(): array
    {
        $items = [];
        if ($this->config->get("items") !== null && !empty($this->config->get("items"))) {
            foreach ($this->config->get("items") as $item) {
                $items[] = Serialize::deserialize($item);
            }
        }
        return $items;
    }

    /**
     * @return mixed|null
     */
    public function getRandomItems(int $count = 1): array
    {
        $items = $this->getItems();
        if (empty($items)) {
            return [];
        }
        $randomKeys = array_rand($items, min($count, count($items)));
        $randomItems = [];
        foreach ((array)$randomKeys as $key) {
            $randomItems[] = $items[$key];
        }
        return $randomItems;
    }

    public function OpenLootbox(Player $player, int $count = 8): void
    {
        $randomItems = $this->getRandomItems($count);
        foreach ($randomItems as $item) {
            $player->getInventory()->addItem($item);
        }
        $player->sendMessage("§aYou received $count random items from the lootbox!");
    }

    public function giveLootbox(Player $player, int $amount = 1): void
    {
        $crateItems = $this->getItems();
        $ItemNames = [];

        foreach ($crateItems as $item) {
            $name = trim($item->getName());
            if ($name !== '') {
                $ItemNames[] = $name;
            }
        }

        $lootbox = VanillaBlocks::ENDER_CHEST()->asItem();
        $lootbox->setCustomName("§r§l§5Lootbox");
        $lootbox->setLore([
            "§r§7Right click to open",
            "§r",
            implode("\n", array_map([TextFormat::class, 'colorize'], $ItemNames)),
            "§r",
            "§r§7Tebex Store: §f" . Loader::getInstance()->getConfig()->get("tebex-crates")
        ]);
        $lootbox->getNamedTag()->setString('Lootbox_Item', 'Lootbox');
        $player->getInventory()->addItem($lootbox);
        $player->sendMessage(Messages::LOOTBOX_GIVE);
    }

    public function EditMenu(Player $player): void
    {
        $menu = InvMenu::create(InvMenuTypeIds::TYPE_CHEST);
        $menu->getInventory()->setContents($this->items);
        $menu->setInventoryCloseListener(function (Player $player, Inventory $inventory): void {
            $this->items = $inventory->getContents();
            $this->setItems($this->items);
            $player->sendMessage(TextFormat::colorize(Messages::LOOTBOX_EDIT));
        });
        $menu->send($player, TextFormat::colorize('&5Lootbox Loot'));
    }
}