<?php

namespace hcf\handler\lootbox;

use pocketmine\item\Item;

class LootboxManager{

    public static $instance;

    public function __construct(){
        self::$instance = new Lootbox();
    }

    public static function getLootbox(): Lootbox {
        return self::$instance;
    }
}