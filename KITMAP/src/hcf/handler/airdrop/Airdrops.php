<?php

/**
*     _    ___ ____  ____  ____   ___  ____  
*    / \  |_ _|  _ \|  _ \|  _ \ / _ \|  _ \ 
*   / _ \  | || |_) | | | | |_) | | | | |_) |
*  / ___ \ | ||  _ <| |_| |  _ <| |_| |  __/ 
* /_/   \_\___|_| \_\____/|_| \_\\___/|_|    
 */

namespace hcf\handler\airdrop;

use hcf\databases\AirdropDatabase;
use hcf\Loader;
use hcf\player\Player;
use hcf\utils\serialize\Serialize;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\inventory\Inventory;
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

    public function setItems(array $items): void
    {
        $content = [];
        foreach ($items as $item) {
            $content[] = Serialize::serialize($item);
        }
    
        if (empty($content)) return;
    
        $conn = AirdropDatabase::getInstance()->getConnection();
        $jsonContent = json_encode($content);
        $stmt = $conn->prepare("REPLACE INTO airdrops (items) VALUES (?)");
        $stmt->bind_param("s", $jsonContent);
        $stmt->execute();
        $stmt->close();
    }

    public function getItems(): array
    {
        $conn = AirdropDatabase::getInstance()->getConnection();
        $stmt = $conn->prepare("SELECT items FROM airdrops");
        $stmt->execute();
        $result = $stmt->get_result();
    
        if($row = $result->fetch_assoc()) {
            $content = json_decode($row['items'], true);
            if(empty($content)) {
                return [];
            }
            
            $items = [];
            foreach($content as $serializedItem) {
                $items[] = Serialize::deserialize($serializedItem);
            }
            return $items;
        }
    
        return [];
    }

    public function getRandomItems()
    {
        $conn = AirdropDatabase::getInstance()->getConnection();
        $stmt = $conn->prepare("SELECT items FROM airdrops");
        $stmt->execute();
        $result = $stmt->get_result();
        
        if($row = $result->fetch_assoc()) {
            $content = json_decode($row['items'], true);
            if(empty($content)) {
                return null;
            }
            
            $items = [];
            foreach($content as $serializedItem) {
                $items[] = Serialize::deserialize($serializedItem);
            }
        
            if(empty($items)) {
                return null;
            }
        
            $randomKey = array_rand($items);
            return $items[$randomKey];
        }
        
        return null;
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
