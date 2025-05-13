<?php

namespace hcf\module\anticheat\checks;

use hcf\Loader;
use hcf\player\Player;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\network\mcpe\protocol\ServerboundPacket;
use pocketmine\network\mcpe\protocol\types\inventory\UseItemOnEntityTransactionData;

class AutoClick{

    private array $clicks = [];
    private $config;

    public function __construct()
    {
        $this->config = Loader::getInstance()->getConfig();
    }

    public function run(ServerboundPacket $packet, Player $player): void {
        if ($packet instanceof InventoryTransactionPacket) {
            $name = $player->getName();

            if (!isset($this->clicks[$name])) {
                $this->clicks[$name] = 0;
            }

            if ($this->isRigthClick($packet)) {
                $this->clicks[$name]++;
            }

            $cps = $this->clicks[$name];
            $checks = $this->config->get("checks");
            if ($cps > $checks["autoclick"]["max_cps_ban"] && $player->getNetworkSession()->getPing() <= 100){
                Loader::getInstance()->getStaffModeManager()->addBanAntiCheat($player, "AutoClick", "30d");
            }elseif ($cps > $checks["autoclick"]["max_cps_alerts"] && $player->getNetworkSession()->getPing() <= 100) {
                Loader::getInstance()->getAntiCheatManager()->AlertStaff($player, "AutoClick", $cps);
            }
        }
    }

    public function isRigthClick(ServerboundPacket $packet): bool {
        if ($packet instanceof InventoryTransactionPacket) {
            if ($packet->trData instanceof UseItemOnEntityTransactionData) {
                return $packet->trData->getActionType() === UseItemOnEntityTransactionData::ACTION_INTERACT;
            }
        }
        return false;
    }
}