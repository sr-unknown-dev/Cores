<?php

namespace hcf\item;

use hcf\item\Fireworks;
use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemTypeIds;
use pocketmine\utils\CloningRegistryTrait;

/**
 * @method static Crossbow CROSSBOW()
 */
class ExtraVanillaItems{
    use CloningRegistryTrait;

    private function __construct(){
        //NOOP
    }

    /**
     * @param string $name
     * @param Item $item
     * @return void
     */
    protected static function register(string $name, Item $item) : void{
        self::_registryRegister($name, $item);
    }

    /**
     * @return Item[]
     */
    public static function getAll() : array{
        /** @var Item[] $result */
        $result = self::_registryGetAll();
        return $result;
    }

    /**
     * @return void
     */
    protected static function setup(): void{
        self::register('fireworks', new Fireworks(new ItemIdentifier(ItemTypeIds::newId()), 'Fireworks'));
    }
}
