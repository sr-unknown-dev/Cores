<?php

namespace hcf\Tasks;

use hcf\Server\ServerA;
use pocketmine\item\Armor;
use pocketmine\item\Durable;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;

class AutoFixTask extends Task {
    private string $playerName;
    private int $repairCounter = 0;
    private const REPAIR_INTERVAL = 5; // Reparar cada 5 ticks

    public function __construct(Player $player) {
        $this->playerName = $player->getName();
    }

    public function onRun(): void {
        $player = $this->getPlayer();
        if ($player === null || !isset(ServerA::$fix[$this->playerName])) {
            $this->getHandler()->cancel();
            return;
        }

        if (++$this->repairCounter < self::REPAIR_INTERVAL) {
            return;
        }
        $this->repairCounter = 0;

        $this->repararInventario($player);
        $this->repararArmadura($player);
    }

    private function getPlayer(): ?Player
    {
        return $this->playerName !== "" ? Server::getInstance()->getPlayerExact($this->playerName) : null;
    }

    private function repararInventario(Player $player): void
    {
        $inventory = $player->getInventory();
        $contents = $inventory->getContents();
        $modified = false;

        foreach ($contents as $slot => $item) {
            if ($item instanceof Durable && $item->getDamage() > 0) {
                $contents[$slot] = $item->setDamage(0);
                $modified = true;
            }
        }

        // Solo actualizar si hay cambios
        if ($modified) {
            $inventory->setContents($contents);
        }
    }

    private function repararArmadura(Player $player): void
    {
        $armorInventory = $player->getArmorInventory();
        $contents = $armorInventory->getContents();
        $modified = false;

        foreach ($contents as $slot => $armor) {
            if ($armor instanceof Armor && $armor->getDamage() > 0) {
                $contents[$slot] = $armor->setDamage(0);
                $modified = true;
            }
        }

        // Solo actualizar si hay cambios
        if ($modified) {
            $armorInventory->setContents($contents);
        }
    }
}