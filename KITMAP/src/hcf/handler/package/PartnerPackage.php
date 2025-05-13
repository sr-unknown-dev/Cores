<?php

namespace hcf\handler\package;

use hcf\handler\handler\package\utils\DataUtils;
use hcf\Loader;
use hcf\player\Player;
use hcf\utils\serialize\Serialize;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\inventory\Inventory;
use pocketmine\item\Item;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

/**
 * Class PartnerPackage
 * @package PartnerPackage\module
 */
class PartnerPackage
{

    private $config;
    private $items = [];

    public function __construct()
    {
        $this->config = new Config(Loader::getInstance()->getDataFolder() . 'package.yml', Config::YAML);
        $this->cargarItems();
    }

    private function cargarItems()
    {
        $contenido = $this->config->get("content", []);
        foreach ($contenido as $itemSerializado) {
            $this->items[] = Serialize::deserialize($itemSerializado);
        }
    }

    public function setItems(array $items)
    {
        $this->items = $items;
        $contenido = [];
        foreach ($items as $item) {
            $contenido[] = Serialize::serialize($item);
        }
        $this->config->set("content", $contenido);
        $this->config->save();
    }

    public function getItems()
    {
        return $this->items;
    }

    public function getRandomItems()
    {
        if (empty($this->items)) {
            return null;
        }

        $random = array_rand($this->items);
        return $this->items[$random];
    }

    public function sendMenu(Player $player): void {
        $menu = InvMenu::create(InvMenuTypeIds::TYPE_DOUBLE_CHEST);
        $menu->getInventory()->setContents($this->items);
        $menu->setInventoryCloseListener(function (Player $player, Inventory $inventory): void {
            $this->setItems($inventory->getContents());
            $player->sendMessage(TextFormat::colorize('&aHas editado el Partner Package'));
        });
        $menu->send($player, TextFormat::colorize('&5Package Edit'));
    }
}