<?php

namespace hcf\module\treasureisland;

use hcf\Loader;
use hcf\module\treasureisland\command\TreasureCommand;
use pocketmine\block\Chest as BlockChest;
use pocketmine\block\tile\Chest;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\TextFormat;

class TreasureIslandManager {

    public function __construct(){
        $this->setItems(Loader::getInstance()->getProvider()->getTreasure());
        Loader::getInstance()->getServer()->getCommandMap()->register("HCF", new TreasureCommand());
        Loader::getInstance()->getScheduler()->scheduleRepeatingTask(new ClosureTask(function (): void {
            $this->update();
        }), 8 * 60 * 60 * 20);
    }
    
    public function update(): void {
        $claim = Loader::getInstance()->getClaimManager()->getClaim("TreasureIsland");
        if($claim == null) return;
        $pos1 = new Vector3($claim->getMinX(), 0, $claim->getMinZ());
        $pos2 = new Vector3($claim->getMaxX(), 255, $claim->getMaxZ());
        for ($x = $pos1->getX(); $x <= $pos2->getX(); $x++) {
            for ($y = $pos1->getY(); $y <= $pos2->getY(); $y++) {
                for ($z = $pos1->getZ(); $z <= $pos2->getZ(); $z++) {
                    $block = Loader::getInstance()->getServer()->getWorldManager()->getDefaultWorld()->getBlockAt($x, $y, $z);
                    if ($block instanceof BlockChest) {
                        $tile = Loader::getInstance()->getServer()->getWorldManager()->getDefaultWorld()->getTileAt($x, $y, $z);
                        if ($tile instanceof Chest) {
                            $chestContents = $this->getItems();
                            $tile->getInventory()->setContents($chestContents);
                            Loader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&r&[TreasureIsland]  the chest in TreasureIsland are refilled &a(x= " . $pos1->x . ", y= " . $pos1->y . ", z= " . $pos2->z . ")"));
                        }
                    }
                }
            }
        }
    }

    public array $items = [];

    public function getItems(): array {
        if (empty($this->items)) {
            return [];
        }
        $array = array_rand($this->items, min(rand(6, 12), count($this->items)));
        $items = [];
        foreach ($array as $slot => $item) {
            $items[] = $this->items[$item];
        }
        return $items;
    }

    public function setItems(array $array): void {
        $this->items = $array;
    }

    public function getRandomItem(): Item {
        return $this->items[array_rand($this->items)];
    }

}