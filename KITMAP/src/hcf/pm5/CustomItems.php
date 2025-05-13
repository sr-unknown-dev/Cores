<?php
declare(strict_types=1);

namespace hcf\pm5;

use pocketmine\data\bedrock\item\ItemTypeNames;
use pocketmine\data\bedrock\item\SavedItemData;
use pocketmine\inventory\CreativeInventory;
use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemTypeIds;
use pocketmine\item\StringToItemParser;
use pocketmine\utils\CloningRegistryTrait;
use pocketmine\world\format\io\GlobalItemDataHandlers;

/**
 * @method static Item EMPTY_MAP()
 * @method static Item GOLDEN_HORSE_ARMOR()
 * @method static Item ENCHANTED_BOOK()
 */
class CustomItems{
	use CloningRegistryTrait;
	public static function registerItem(string $name, Item $item, string $namespace) : void{
		StringToItemParser::getInstance()->override($item->getName(), static fn() => clone $item);
		GlobalItemDataHandlers::getDeserializer()->map($namespace, fn() => clone $item);
		GlobalItemDataHandlers::getSerializer()->map(clone $item, fn() => new SavedItemData($namespace));
		CreativeInventory::getInstance()->add($item);
		self::_registryRegister($name, $item);
	}

	protected static function register(string $name, Item $member) : void{
		self::_registryRegister($name, $member);
	}

	protected static function setup() : void{
		self::registerItem("EMPTY_MAP", new Item(new ItemIdentifier(ItemTypeIds::newId()), "Empty Map"),ItemTypeNames::EMPTY_MAP);
		self::registerItem("GOLDEN_HORSE_ARMOR", new Item(new ItemIdentifier(ItemTypeIds::newId()), "Golden Horse Armor"),ItemTypeNames::GOLDEN_HORSE_ARMOR);
		self::registerItem("ENCHANTED_BOOK", new Item(new ItemIdentifier(ItemTypeIds::newId()), "Enchanted Book"),ItemTypeNames::ENCHANTED_BOOK);

	}
}