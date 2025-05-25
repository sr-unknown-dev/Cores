<?php

namespace unknown\practice\Kit;

use pocketmine\utils\Config;
use unknown\practice\Kit\commands\KitCommands;
use unknown\practice\Loader;

class KitManager
{
    /** @var Kit[] */
    private array $kits = [];
    private Config $config;

    private static ?KitManager $instance = null;

    public static function getInstance(): KitManager
    {
        return self::$instance ??= new self();
    }

    public function __construct()
    {
        $this->config = new Config(Loader::getInstance()->getDataFolder() . "kits.json", Config::JSON);
        $this->loadKits();

        Loader::getInstance()->getServer()->getCommandMap()->register("kit", new KitCommands());
    }

    public function createKit(Kit $kit): void
    {
        $this->kits[$kit->getName()] = $kit;
        $this->saveKits();
    }

    public function removeKit(string $kitName): void
    {
        unset($this->kits[$kitName]);
        $this->config->remove($kitName);
        $this->config->save();
    }

    public function getKit(string $kitName): ?Kit
    {
        return $this->kits[$kitName] ?? null;
    }

    public function getKits(): array
    {
        return $this->kits;
    }

    private function loadKits(): void
    {
        foreach ($this->config->getAll() as $name => $data) {
            $this->kits[$name] = new Kit(
                $data['name'],
                $data['items'] ?? [],
                $data['armor'] ?? []
            );
        }
    }

    private function saveKits(): void
    {
        foreach ($this->kits as $kit) {
            $this->config->set($kit->getName(), $kit->Data());
        }
        $this->config->save();
    }
}
