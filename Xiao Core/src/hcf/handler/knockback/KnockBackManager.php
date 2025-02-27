<?php

namespace hcf\handler\knockback;

use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\Config;
use hcf\Loader;

class KnockBackManager implements Listener {

    private $knockbackHorizontal;
    private $knockbackVertical;
    private $knockbackDelay;
    private $config;

    public function __construct() {
        $this->cargarConfiguracion();
    }

    private function cargarConfiguracion(): void {
        $this->config = new Config(Loader::getInstance()->getDataFolder() . "knockback.yml", Config::YAML);
        $this->actualizarValores();
    }

    private function actualizarValores(): void {
        $this->knockbackHorizontal = $this->config->get('knockback.horizontal', 0.4);
        $this->knockbackVertical = $this->config->get('knockback.vertical', 0.4);
        $this->knockbackDelay = $this->config->get('knockback.delay', 0);
    }

    public function setHorizontal(float $valor): void {
        $this->config->set('knockback.horizontal', $valor);
        $this->config->save();
        $this->actualizarValores();
    }

    public function setVertical(float $valor): void {
        $this->config->set('knockback.vertical', $valor);
        $this->config->save();
        $this->actualizarValores();
    }

    public function setDelay(int $valor): void {
        $this->config->set('knockback.delay', $valor);
        $this->config->save();
        $this->actualizarValores();
    }

    public function getHorizontal(): float {
        return $this->knockbackHorizontal;
    }

    public function getVertical(): float {
        return $this->knockbackVertical;
    }
    
    public function getDelay(): int {
        return $this->knockbackDelay;
    }
}
