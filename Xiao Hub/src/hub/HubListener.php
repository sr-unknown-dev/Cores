<?php

namespace hub;

use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\item\VanillaItems;
use pocketmine\utils\TextFormat;
use hub\player\Player;

class HubListener implements Listener
{
    public function handleJoin(PlayerJoinEvent $event): void
    {
        /** @var Player */
        $player = $event->getPlayer();
        $player->join();

        $joinMessage = str_replace('{player}', $player->getName(), Loader::getInstance()->getConfig()->get('join.message'));
        $event->setJoinMessage(TextFormat::colorize($joinMessage));
        $items = [];

        $compass = VanillaItems::COMPASS();
        $compass->setCustomName("§aModalitys");
        $compass->setLore(["§7Click for random teleport\n§3@StaffTeam"]);
        $compass->getNamedTag()->setString("hub", "Modalitys");
        $items[0] = $compass;

        $pearl = VanillaItems::ENDER_PEARL();
        $pearl->setCustomName("§3EnderPearl Debuff");
        $pearl->setLore(["§7Click to freeze player\n§3@StaffTeam"]);
        $pearl->getNamedTag()->setString("hub", "Debuff");
        $items[1] = $pearl;

        $player->getInventory()->setContents($items);
    }

    public function handleItemUse(PlayerItemUseEvent $event):void
    {
        $player = $event->getPlayer();
        $itemHand = $player->getInventory()->getItemInHand();

        if ($itemHand->getNamedTag()->getTag("hub") && $itemHand->getNamedTag()->getString("hub") === "Modalitys"){

        }

        if ($itemHand->getNamedTag()->getTag("hub") && $itemHand->getNamedTag()->getString("hub") === "Debuff"){
            $direction = $player->getDirectionVector();
            $player->setMotion($direction->multiply(2));
        }
    }
}