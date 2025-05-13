<?php

namespace hcf\handler\lootbox;

use hcf\Loader;
use hcf\utils\messages\Messages;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\utils\TextFormat;

class LootboxListener implements Listener
{
    private $instance;

    public function __construct()
    {
        $this->instance = Loader::getInstance()->getLootboxManager();
    }

    public function handleItemUse(PlayerItemUseEvent $ev): void
    {
        $player = $ev->getPlayer();
        $itemHand = $player->getInventory()->getItemInHand();

        if ($itemHand->getNamedTag()->getTag("Lootbox_Item") && $itemHand->getNamedTag()->getString("Lootbox_Item") === "Lootbox") {
            $randomItems = $this->instance->getLootbox()->getRandomItems(8);
            foreach ($randomItems as $item) {
                $player->getInventory()->addItem($item);
            }

            $lootboxItems = $this->instance->getLootbox()->getItems();
            $ItemNames = [];

            foreach ($lootboxItems as $item) {
                $name = trim($item->getName());
                if ($name !== '') {
                    $ItemNames[] = $name;
                }
            }

            $msg = str_replace("{items}", implode("\n", array_map([TextFormat::class, 'colorize'], $ItemNames)), Messages::LOOTBOX_ITEMS_GIVE);
            $player->sendMessage($msg);
        }
    }
}
