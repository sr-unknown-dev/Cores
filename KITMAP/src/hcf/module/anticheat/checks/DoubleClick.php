<?php

namespace hcf\module\anticheat\checks;

use hcf\Loader;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\player\Player;

class DoubleClick implements Listener {

    private $lastMSClick = [];

    /**
     * @param EntityDamageByEntityEvent $event
     * Detecta doble clic.
     */
    public function onEntityDamage(EntityDamageByEntityEvent $event): void {
        $damager = $event->getDamager();
        $victim = $event->getEntity();

        if ($damager instanceof Player && $victim instanceof Player) {
            $uuid = $damager->getUniqueId()->toString();
            $currentTime = microtime(true) * 1000;

            if (!isset($this->lastMSClick[$uuid])) {
                $this->lastMSClick[$uuid] = [0, 0];
            }

            $first = $this->lastMSClick[$uuid][0];
            $second = $this->lastMSClick[$uuid][1];

            if ($first == 0) {
                $this->lastMSClick[$uuid][0] = $currentTime;
            } elseif ($second == 0) {
                $this->lastMSClick[$uuid][1] = $currentTime;
                $interval = $this->lastMSClick[$uuid][1] - $this->lastMSClick[$uuid][0];
                $this->lastMSClick[$uuid][0] = $currentTime;
                if ($interval < 50) {
                    Loader::getInstance()->getAntiCheatManager()->AlertStaff($damager, "DoubleClick", 1);
                }
            } else {
                $interval = $currentTime - $this->lastMSClick[$uuid][1];
                $this->lastMSClick[$uuid][0] = $currentTime;
                $this->lastMSClick[$uuid][1] = 0;
                if ($interval < 50) {
                    Loader::getInstance()->getAntiCheatManager()->AlertStaff($damager, "DoubleClick", 1);
                }
            }
        }
    }
}
