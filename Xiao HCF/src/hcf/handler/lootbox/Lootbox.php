<?php

namespace hcf\handler\lootbox;

use hcf\Loader;
use hcf\player\Player;
use hcf\utils\serialize\Serialize;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\inventory\Inventory;
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
    public function getRandomItems(): mixed
    {
        $items = $this->getItems();
        if (empty($items)) {
            return null;
        }
        $randomKey = array_rand($items);
        return $items[$randomKey];
    }

    public function sendMenu(Player $player): void {
        $menu = InvMenu::create(InvMenuTypeIds::TYPE_CHEST);
        $menu->getInventory()->setContents($this->items);
        $menu->setInventoryCloseListener(function (Player $player, Inventory $inventory): void {
            $this->items = $inventory->getContents();
            $this->setItems($this->items);
            $player->sendMessage(TextFormat::colorize('&aYou have edited the loot.'));
        });
        $menu->send($player, TextFormat::colorize('&3Airdrop Loot'));
    }
}