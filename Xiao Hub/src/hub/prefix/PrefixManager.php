<?php

namespace hub\prefix;

use hub\prefix\command\PrefixCommand;
use hub\prefix\entity\PrefixEntity;
use hub\Loader;
use hub\player\Player;
use pocketmine\permission\DefaultPermissions;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionManager;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\world\World;

class PrefixManager
{
    private array $prefixes = [];
    
    public function __construct()
    {
        # Register Entity
		EntityFactory::getInstance()->register(PrefixEntity::class, function (World $world, CompoundTag $nbt): PrefixEntity {
            return new PrefixEntity(EntityDataHelper::parseLocation($nbt, $world), PrefixEntity::parseSkinNBT($nbt), $nbt);
        }, ['PrefixEntity']);
        # Register command
        Loader::getInstance()->getServer()->getCommandMap()->register('HCF', new PrefixCommand());
        # Register prefixes
        foreach (Loader::getInstance()->getProvider()->getPrefixes() as $name => $data) {
            $this->createPrefix((string) $name, $data);
            if ($data['permission'] !== null) {
                $this->registerPermission($data['permission']);
            }
        }
    }
    
    public function getPrefixes(): array
    {
        return $this->prefixes;
    }
    
    public function getPrefix(string $name): ?Prefix
    {
        return $this->prefixes[$name] ?? null;
    }
    
    public function createPrefix(string $name, array $data): void
    {
        $this->prefixes[$name] = new Prefix($name, $data);
    }
    
    public function registerPermission(string $permission): void {
        $manager = PermissionManager::getInstance();
        $manager->addPermission(new Permission($permission));
        $manager->getPermission(DefaultPermissions::ROOT_OPERATOR)->addChild($permission, true);
    }
    
    public function removePrefix(string $name): void
    {
        unset($this->prefixes[$name]);
        
        if (file_exists(Loader::getInstance()->getDataFolder() . 'database' . DIRECTORY_SEPARATOR . 'prefixes' . DIRECTORY_SEPARATOR . $name . '.yml')) {
            $result = unlink(Loader::getInstance()->getDataFolder() . 'database' . DIRECTORY_SEPARATOR . 'prefixes' . DIRECTORY_SEPARATOR . $name . '.yml');

            if ($result) {
                Loader::getInstance()->getLogger()->debug('Prefix ' . $name . ' file deleted successfully');
            } else {
                Loader::getInstance()->getLogger()->debug('Error for deleted Prefix ' . $name . ' file');
            }
        }
    }
}