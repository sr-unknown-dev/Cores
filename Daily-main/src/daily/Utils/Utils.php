<?php

namespace daily\Utils;

use daily\Loader;
use pocketmine\utils\Config;
use pocketmine\item\Item;
use pocketmine\item\StringToItemParser;

class Utils
{

    public static function getConfig(){
        return new Config(Loader::getInstance()->getDataFolder(). "players.yml", Config::YAML);
    }

    public static function getItemsConfig(){
        return new Config(Loader::getInstance()->getDataFolder(). "items.yml", Config::YAML);
    }

    public static function serialize(Item $item) : string {
        $itemToJson = self::itemToJson($item);
        return base64_encode(gzcompress($itemToJson));
    }

    public static function deserialize(string $item): Item {
        $itemFromJson = gzuncompress(base64_decode($item));
        return self::jsonToItem($itemFromJson);
    }

    public static function itemToJson(Item $item) : string {
        $cloneItem = clone $item;
        $itemNBT = $cloneItem->nbtSerialize();
        return base64_encode(serialize($itemNBT));
    }

    public static function jsonToItem(string $json) : Item {
        $itemNBT = unserialize(base64_decode($json));
        return Item::nbtDeserialize($itemNBT);
    }
}