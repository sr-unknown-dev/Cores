<?php

namespace hcf\Tasks;

use pocketmine\scheduler\Task;
use pocketmine\player\Player;
use hcf\Server\AutoFix;
use pocketmine\item\Armor;
use pocketmine\item\Durable;

class AutoFixTask extends Task {

    private Player $player;

    public function __construct(Player $player) {
        $this->player = $player;
    }

    public function onRun(): void {
        if (!$this->player->isOnline() || !isset(AutoFix::$fix[$this->player->getName()])) {
            $this->getHandler()->cancel();
            return;
        }

        $this->repararInventario();
        $this->repararArmadura();
    }

    private function repararInventario(): void {
        foreach ($this->player->getInventory()->getContents() as $slot => $item) {
            if ($item instanceof Durable && $item->getDamage() > 0) {
                $this->player->getInventory()->setItem($slot, $item->setDamage(0));
            }
        }
    }

    private function repararArmadura(): void {
        foreach ($this->player->getArmorInventory()->getContents() as $slot => $armor) {
            if ($armor instanceof Armor && $armor->getDamage() > 0) {
                $this->player->getArmorInventory()->setItem($slot, $armor->setDamage(0));
            }
        }
    }
}
