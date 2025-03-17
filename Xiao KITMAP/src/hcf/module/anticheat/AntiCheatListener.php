<?php

namespace hcf\module\anticheat;

use hcf\Loader;
use hcf\player\Player;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\Server;
use pocketmine\utils\Config;

class AntiCheatListener implements Listener
{
    private array $violations = [];
    private array $clickTimestamps = [];
    private Config $config;
    private AntiCheatManager $manager;

    public function __construct(AntiCheatManager $manager)
    {
        $this->config = Loader::getInstance()->getConfig();
        $this->manager = $manager;
    }

    /**
     *
     * @param PlayerMoveEvent $event
     * @priority HIGHEST
     */
    public function onPlayerMove(PlayerMoveEvent $event): void
    {
        if (!$this->config->getNested('checks.speed.enabled', false)) {
            return;
        }

        $player = $event->getPlayer();
        if (!$player instanceof Player || $player->isSpectator() || $this->manager->hasExemption($player)) {
            return;
        }

        $from = $event->getFrom();
        $to = $event->getTo();
        $speed = $from->distance($to);

        $maxSpeed = $this->config->getNested('checks.speed.max_speed', 1);
        if ($speed > $maxSpeed) {
            $this->recordViolation($player, 'Speed', $speed, 'max_speed_alerts', 'max_speed_ban');
        }
    }

    /**
     *
     * @param PlayerInteractEvent $event
     * @priority HIGHEST
     */
    public function onPlayerInteract(PlayerInteractEvent $event): void
    {
        if (!$this->config->getNested('checks.autoclick.enabled', false)) {
            return;
        }

        $player = $event->getPlayer();
        if (!$player instanceof Player || $this->manager->hasExemption($player)) {
            return;
        }

        $name = $player->getName();
        $currentTime = microtime(true);

        if (!isset($this->clickTimestamps[$name])) {
            $this->clickTimestamps[$name] = [];
        }

        $this->clickTimestamps[$name] = array_filter(
            $this->clickTimestamps[$name],
            static function($timestamp) use ($currentTime) {
                return ($currentTime - $timestamp) <= 1;
            }
        );

        $this->clickTimestamps[$name][] = $currentTime;
        $clicks = count($this->clickTimestamps[$name]);

        $maxCPS = $this->config->getNested('checks.autoclick.max_cps', 20);
        if ($clicks > $maxCPS) {
            $this->recordViolation($player, 'AutoClick', $clicks, 'max_cps_alerts', 'max_cps_ban');
        }
    }

    /**
     *
     * @param EntityDamageByEntityEvent $event
     * @priority HIGHEST
     */
    public function onEntityDamageByEntity(EntityDamageByEntityEvent $event): void
    {
        if (!$this->config->getNested('checks.reach.enabled', false)) {
            return;
        }

        $damager = $event->getDamager();
        if (!$damager instanceof Player || $this->manager->hasExemption($damager)) {
            return;
        }

        $victim = $event->getEntity();
        $reach = $damager->getLocation()->distance($victim->getLocation());

        $maxReach = $this->config->getNested('checks.reach.max_reach', 4);
        if ($reach > $maxReach) {
            $this->recordViolation($damager, 'Reach', $reach, 'max_reach_alerts', 'max_reach_ban');
        }
    }

    /**
     *
     * @param Player $player
     * @param string $checkType
     * @param float $value
     * @param string $alertThresholdKey
     * @param string $banThresholdKey
     * @return void
     */
    private function recordViolation(Player $player, string $checkType, float $value, string $alertThresholdKey, string $banThresholdKey): void
    {
        $name = $player->getName();

        $this->violations[$name] = ($this->violations[$name] ?? 0) + 1;

        $alertThreshold = $this->config->getNested("checks.$checkType.$alertThresholdKey", 0);
        $banThreshold = $this->config->getNested("checks.$checkType.$banThresholdKey", 0);
        $maxAlertsBan = $this->config->getNested('alerts.max_alerts_ban', 5);

        if ($this->violations[$name] >= $maxAlertsBan || $value >= $banThreshold) {
            $this->manager->punishments($player, $checkType);
            unset($this->violations[$name]);
        } elseif ($value > $alertThreshold) {
            $this->manager->alertStaff($player, $checkType, $this->violations[$name]);
        }
    }
}
