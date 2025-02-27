<?php

declare(strict_types=1);

namespace hcf\item;

use pocketmine\data\bedrock\item\ItemSerializer;
use pocketmine\data\bedrock\item\ItemTypeNames;
use pocketmine\data\bedrock\item\SavedItemData;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\PotionType;
use pocketmine\item\StringToItemParser;
use pocketmine\item\VanillaItems;
use pocketmine\world\format\io\GlobalItemDataHandlers;

class ItemManager
{
    
    /**
     * ItemManager construct.
     */
    public function __construct() {

        $itemDeserializer = GlobalItemDataHandlers::getDeserializer();
                $itemSerializer = GlobalItemDataHandlers::getSerializer();
                $stringToItemParser = StringToItemParser::getInstance();

		$fireworks = ExtraVanillaItems::FIREWORKS();
                $itemDeserializer->map(ItemTypeNames::FIREWORK_ROCKET, static fn() => clone $fireworks);
                $itemSerializer->map($fireworks, static fn() => new SavedItemData(ItemTypeNames::FIREWORK_ROCKET));
                $stringToItemParser->register("firework_rocket", static fn() => clone $fireworks);

    }
        //ItemFactory::getInstance()->register(new EnderpearlItem(), true);

        
        //foreach(PotionType::getAll() as $type)
            //ItemFactory::getInstance()->register(new SplashPotionItem($type), true);
}
