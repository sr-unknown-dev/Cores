<?php

namespace y;

use hcf\Loader;
use hcf\utils\serialize\Serialize;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\Config;

class KitsManage {
    
    public function Frutas(string $fruta, string $info){

        $frutas = [
            "manzana" => [
                "calorias" => 50,
                "cantidad" => 10,
                "precio" => 100
            ],
            "pera" => [
                "calorias" => 70,
                "cantidad" => 15,
                "precio" => 150
            ],
            "uva" => [
                "calorias" => 60,
                "cantidad" => 20,
                "precio" => 200
            ],
            "banana" => [
                "calorias" => 55,
                "cantidad" => 12,
                "precio" => 120
            ],
            "kiwi" => [
                "calorias" => 65,
                "cantidad" => 18,
                "precio" => 180
            ],
        ];

        return $fruta[$fruta][$info] ?? "N/A";
    }
}