<?php

declare(strict_types=1);

namespace hcf\provider;

use hcf\Loader;
use hcf\utils\serialize\Serialize;
use hcf\pm5\ItemSerializer;
use pocketmine\utils\Config;

class Provider
{

    /** @var Config */
    public Config $abilitiesConfig, $treasureConfig, $kothConfig, $claimConfig, $kitOpConfig, $kitFreeConfig, $kitPayConfig, $vkitConfig, $crateConfig, $reclaimConfig, $airdropConfig;

    /**
     * YamlProvider construct
     */
    public function __construct()
    {
        $plugin = Loader::getInstance();

        # Creation of folders that do not exist
        if (!is_dir($plugin->getDataFolder() . 'database'))
            @mkdir($plugin->getDataFolder() . 'database');

        if (!is_dir($plugin->getDataFolder() . 'database' . DIRECTORY_SEPARATOR . 'players'))
            @mkdir($plugin->getDataFolder() . 'database' . DIRECTORY_SEPARATOR . 'players');

        if (!is_dir($plugin->getDataFolder() . 'database' . DIRECTORY_SEPARATOR . 'factions'))
            @mkdir($plugin->getDataFolder() . 'database' . DIRECTORY_SEPARATOR . 'factions');
        
        if (!is_dir($plugin->getDataFolder() . 'database' . DIRECTORY_SEPARATOR . 'prefixes'))
            @mkdir($plugin->getDataFolder() . 'database' . DIRECTORY_SEPARATOR . 'prefixes');

        # Save default config
        $plugin->saveDefaultConfig();

        $this->kothConfig = new Config($plugin->getDataFolder() . 'database' . DIRECTORY_SEPARATOR . 'koths.yml', Config::YAML);
        $this->claimConfig = new Config($plugin->getDataFolder() . 'database' . DIRECTORY_SEPARATOR . 'claims.yml', Config::YAML);
        $this->reclaimConfig = new Config($plugin->getDataFolder() . 'database' . DIRECTORY_SEPARATOR . 'reclaims.yml', Config::YAML);
        $this->reclaimConfig = new Config($plugin->getDataFolder() . 'database' . DIRECTORY_SEPARATOR . 'reclaims.yml', Config::YAML);
        $this->crateConfig = new Config($plugin->getDataFolder() . 'database' . DIRECTORY_SEPARATOR . 'crates.yml', Config::YAML);
        $this->kitPayConfig = new Config($plugin->getDataFolder() . 'database' . DIRECTORY_SEPARATOR . 'kitsPay.yml', Config::YAML, ["organization" => [], "kits" => []]);
        $this->kitOpConfig = new Config($plugin->getDataFolder() . 'database' . DIRECTORY_SEPARATOR . 'kitsOp.yml', Config::YAML, ["organization" => [], "kits" => []]);
        $this->kitFreeConfig = new Config($plugin->getDataFolder() . 'database' . DIRECTORY_SEPARATOR . 'kitsFree.yml', Config::YAML, ["organization" => [], "kits" => []]);
        $this->vkitConfig = new Config($plugin->getDataFolder() . 'database' . DIRECTORY_SEPARATOR . 'vkits.json', Config::JSON, ["organization" => [], "vkits" => []]);
        //$this->abilitiesConfig = new Config($plugin->getDataFolder() . 'database' . DIRECTORY_SEPARATOR . 'abilities.yml', Config::YAML);
        $this->treasureConfig = new Config($plugin->getDataFolder() . 'database' . DIRECTORY_SEPARATOR . 'treasure.yml', Config::YAML);
    }

    public function save(): void {
        $this->savePlayers();
        $this->saveFactions();
        $this->savePrefixes();
        $this->saveKoths();
        $this->saveClaims();
        $this->saveKits();
        $this->saveKitsPay();
        $this->saveCrates();
        $this->saveTreasure();
        $this->saveReclaims();
        $this->saveKitsOp();
    }

    public function getTreasureConfig(): Config
    {
        return $this->treasureConfig;
    }

    public function getAirdropConfig(): Config
    {
        return $this->airdropConfig;
    }

    public function getKitConfig(): Config
    {
        return $this->kitFreeConfig;
    }

    public function getKitPayConfig(): Config
    {
        return $this->kitPayConfig;
    }
    
    /**   public function getvKitConfig(): Config
    {
        return $this->vkitConfig;
    } */

    public function getAbilitiesConfig(): Config
    {
        return $this->abilitiesConfig;
    }

    /**
     * @return Config
     */
    public function getKothConfig(): Config
    {
        return $this->kothConfig;
    }

    /**
     * @return Config
     */
    public function getClaimsConfig(): Config
    {
        return $this->claimConfig;
    }

    public function getCrateConfig(): Config
    {
        return $this->crateConfig;
    }

    public function getKitOpConfig(): Config
    {
        return $this->kitOpConfig;
    }

    public function getAbilities() : array {
        $items = [];

        foreach($this->getAbilitiesConfig() as $data){
            $result = $data->getAll();
            if(isset($result["items"])){
                foreach($result["items"] as $item){
                    $items[] = Serialize::deserialize($item);
                }
            }
        }
        return $items;
    }

    public function getTreasure() : array {
        $items = [];

        foreach($this->getTreasureConfig() as $data){
            $result = $data->getAll();
            if(isset($result["items"])){
                foreach($result["items"] as $item){
                    $items[] = Serialize::deserialize($item);
                }
            }
        }
        return $items;
    }

    public function getReclaims(): array
    {
        $reclaims = [];

        foreach ($this->reclaimConfig->getAll() as $name => $data) {
            $reclaim = [
                'permission' => $data['permission'],
                'time' => (int) $data['time'],
                'contents' => []
            ];

            if (isset($data['contents'])) {
                foreach ($data['contents'] as $item)
                    $reclaim['contents'][] = Serialize::deserialize($item);
            }
            $reclaims[$name] = $reclaim;
        }
        return $reclaims;
    }

    public function getCrates(): array
    {
        $crates = [];

        foreach ($this->crateConfig->getAll() as $name => $data) {
            if (isset($data['items'])) {
                foreach ($data['items'] as $slot => $item)
                    $data['items'][$slot] = Serialize::deserialize($item);
            }
            $data['key'] = Serialize::deserialize($data['key']);
            $crates[$name] = $data;
        }
        return $crates;
    }

    /**
     * @return array
     */
    public function getPlayers(): array
    {
        $players = [];

        foreach (glob(Loader::getInstance()->getDataFolder() . 'database' . DIRECTORY_SEPARATOR . 'players' . DIRECTORY_SEPARATOR . '*.yml') as $file)
            $players[basename($file, '.yml')] = (new Config(Loader::getInstance()->getDataFolder() . 'database' . DIRECTORY_SEPARATOR . 'players' . DIRECTORY_SEPARATOR . basename($file), Config::YAML))->getAll();
        return $players;
    }

    /**
     * @return array
     */
    public function getFactions(): array
    {
        $factions = [];

        foreach (glob(Loader::getInstance()->getDataFolder() . 'database' . DIRECTORY_SEPARATOR . 'factions' . DIRECTORY_SEPARATOR . '*.yml') as $file)
            $factions[basename($file, '.yml')] = (new Config(Loader::getInstance()->getDataFolder() . 'database' . DIRECTORY_SEPARATOR . 'factions' . DIRECTORY_SEPARATOR . basename($file), Config::YAML))->getAll();
        return $factions;
    }
    
    public function getPrefixes(): array
    {
        $prefixes = [];

        foreach (glob(Loader::getInstance()->getDataFolder() . 'database' . DIRECTORY_SEPARATOR . 'prefixes' . DIRECTORY_SEPARATOR . '*.yml') as $file)
            $prefixes[basename($file, '.yml')] = (new Config(Loader::getInstance()->getDataFolder() . 'database' . DIRECTORY_SEPARATOR . 'prefixes' . DIRECTORY_SEPARATOR . basename($file), Config::YAML))->getAll();
        return $prefixes;
    }


    /**
     * @return array
     */
    public function getKoths(): array
    {
        $koths = [];

        foreach ($this->kothConfig->getAll() as $name => $data) {
            $koths[$name] = $data;
        }
        return $koths;
    }

    /**
     * @return array
     */
    public function getClaims(): array
    {
        $claims = [];

        foreach ($this->claimConfig->getAll() as $name => $data) {
            $claims[$name] = $data;
        }
        return $claims;
    }

    public function getKits(): array
    {
        $kits = [];

        foreach ($this->kitFreeConfig->get('kits') as $name => $data) {
            if ($data['representativeItem'] !== null)
                $data['representativeItem'] = Serialize::deserialize($data['representativeItem']);

            if (isset($data['items'])) {
                foreach ($data['items'] as $slot => $item){
                    $data['items'][$slot] = Serialize::deserialize($item);
                }
            }

            if (isset($data['armor'])) {
                foreach ($data['armor'] as $slot => $armor){
                    $data['armor'][$slot] = Serialize::deserialize($armor);
                }
            }
            $kits[$name] = $data;
        }
        return $kits;
    }

    public function getKitsPay(): array
    {
        $kits = [];

        foreach ($this->kitPayConfig->get('kits') as $name => $data) {
            if ($data['representativeItem'] !== null)
                $data['representativeItem'] = Serialize::deserialize($data['representativeItem']);

            if (isset($data['items'])) {
                foreach ($data['items'] as $slot => $item){
                    $data['items'][$slot] = Serialize::deserialize($item);
                }
            }

            if (isset($data['armor'])) {
                foreach ($data['armor'] as $slot => $armor){
                    $data['armor'][$slot] = Serialize::deserialize($armor);
                }
            }
            $kits[$name] = $data;
        }
        return $kits;
    }
    
    public function getKitsOp(): array{
        $kits = [];

        foreach ($this->kitOpConfig->get('kits') as $name => $data) {
            if ($data['representativeItem'] !== null)
                $data['representativeItem'] = Serialize::deserialize($data['representativeItem']);

            if (isset($data['items'])) {
                foreach ($data['items'] as $slot => $item)
                    $data['items'][$slot] = Serialize::deserialize($item);
            }

            if (isset($data['armor'])) {
                foreach ($data['armor'] as $slot => $armor)
                    $data['armor'][$slot] = Serialize::deserialize($armor);
            }
            $kits[$name] = $data;
        }
        return $kits;
    }

    public function saveTreasure() : void {
        $itemData = [];
        foreach(Loader::getInstance()->getModuleManager()->getTreasureIslandManager()->items as $item){
            $itemData[] = Serialize::serialize($item);
        }
        $this->treasureConfig->set("items", $itemData);
        $this->treasureConfig->save();
    }

    public function saveCrates(): void
    {
        $crates = [];

        foreach (Loader::getInstance()->getHandlerManager()->getCrateManager()->getCrates() as $crate) {
            $crates[$crate->getName()] = $crate->getData();
        }
        $this->crateConfig->setAll($crates);
        $this->crateConfig->save();
    }

    public function saveReclaims(): void
    {
        $reclaims = [];

        foreach (Loader::getInstance()->getHandlerManager()->getReclaimManager()->getReclaims() as $reclaim) {
            $reclaims[$reclaim->getName()] = $reclaim->getData();
        }
        $this->reclaimConfig->setAll($reclaims);
        $this->reclaimConfig->save();
    }

    public function savePlayers(): void
    {
        foreach (Loader::getInstance()->getSessionManager()->getSessions() as $xuid => $session) {
            $config = new Config(Loader::getInstance()->getDataFolder() . 'database' . DIRECTORY_SEPARATOR . 'players' . DIRECTORY_SEPARATOR  . $xuid . '.yml', Config::YAML);
            $config->setAll($session->getData());
            $config->save();
        }
    }

    public function saveFactions(): void
    {
        foreach (Loader::getInstance()->getFactionManager()->getFactions() as $name => $faction) {
            $config = new Config(Loader::getInstance()->getDataFolder() . 'database' . DIRECTORY_SEPARATOR . 'factions' . DIRECTORY_SEPARATOR . $name . '.yml', Config::YAML);
            $config->setAll($faction->getData());
            $config->save();
        }
    }
    
    public function savePrefixes(): void
    {
        foreach (Loader::getInstance()->getPrefixManager()->getPrefixes() as $name => $prefix) {
            $config = new Config(Loader::getInstance()->getDataFolder() . 'database' . DIRECTORY_SEPARATOR . 'prefixes' . DIRECTORY_SEPARATOR . $name . '.yml', Config::YAML);
            $config->setAll($prefix->getData());
            $config->save();
        }
    }

    public function saveKoths(): void
    {
        $koths = [];

        foreach (Loader::getInstance()->getKothManager()->getKoths() as $koth) {
            $koths[$koth->getName()] = $koth->getData();
        }
        $this->kothConfig->setAll($koths);
        $this->kothConfig->save();
    }

    public function saveKits(): void
    {
        $kits = [];

        foreach (Loader::getInstance()->getHandlerManager()->getKitManager()->getKits() as $kit) {
            $kits[$kit->getName()] = $kit->getData();
        }
        $this->kitFreeConfig->set('kits', $kits);
        $this->kitFreeConfig->save();
    }

    public function saveKitsPay(): void
    {
        $kits = [];

        foreach (Loader::getInstance()->getHandlerManager()->getKitPayManager()->getKits() as $kit) {
            $kits[$kit->getName()] = $kit->getData();
        }
        $this->kitPayConfig->set('kits', $kits);
        $this->kitPayConfig->save();
    }

    public function saveKitsOp(): void
    {
        $kits = [];

        foreach (Loader::getInstance()->getHandlerManager()->getKitOpManager()->getKits() as $kit) {
            $kits[$kit->getName()] = $kit->getData();
        }
        $this->kitOpConfig->set('kits', $kits);
        $this->kitOpConfig->save();
    }

    public function saveClaims(): void
    {
        $claims = [];

        foreach (Loader::getInstance()->getClaimManager()->getClaims() as $name => $claim) {
            $claims[$name] = $claim->getData();
        }
        $this->claimConfig->setAll($claims);
        $this->claimConfig->save();
    }
}