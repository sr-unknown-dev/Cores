<?php

/**
*     _    ___ ____  ____  ____   ___  ____  
*    / \  |_ _|  _ \|  _ \|  _ \ / _ \|  _ \ 
*   / _ \  | || |_) | | | | |_) | | | | |_) |
*  / ___ \ | ||  _ <| |_| |  _ <| |_| |  __/ 
* /_/   \_\___|_| \_\____/|_| \_\\___/|_|    
 */

namespace hcf\handler\airdrop;

use hcf\Loader;
use hcf\player\Player;
use hcf\utils\serialize\Serialize;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\type\InvMenuType;
use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\inventory\Inventory;
use pocketmine\item\Item;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

class Airdrops
{
    public $config;
    public $items = [];


    public function __construct()
    {
        $this->config = new Config(Loader::getInstance()->getDataFolder() . "airdrop.yml", Config::YAML);
    }

    public function setItems(array $items)
    {
        $content = [];
        foreach ($items as $item){
            $content[] = Serialize::serialize($item);
        }
        $this->config->set("items", $content);
        $this->config->save();
    }

    /**
     * Get the value of items
     */ 
    public function getItems()
    {
        $content = [];
        foreach ($this->config->get("items", []) as $items){
            $content[] = Serialize::deserialize($items);
        }
        return $content;
    }

    public function getRandomItems()
    {
        $items = $this->getItems();

        if (empty($items)){
            return null;
        }

        $random = array_rand($items);
        return $items[$random];
    }

    public function sendMenu(Player $player): void {
        $menu = InvMenu::create(InvMenuTypeIds::TYPE_CHEST);
        $menu->getInventory()->setContents($this->items);
        $menu->setInventoryCloseListener(function (Player $player, Inventory $inventory): void {
            $this->items = $inventory->getContents();
            $this->setItems($this->items);
            $player->sendMessage(TextFormat::colorize('&aYou haven edit the loot.'));
        });
        $menu->send($player, TextFormat::colorize('&3Airdrop Loot'));
    }
}
