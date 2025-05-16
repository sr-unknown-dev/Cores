<?php

namespace unknown\events;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerJumpEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use unknown\Loader;
use unknown\menu\Menu;
use unknown\scoreboard\Scoreboard;

class Events implements Listener
{

    public function handleJoin(PlayerJoinEvent $e): void
    {
        $player = $e->getPlayer();

        if (Loader::getInstance()->chatMuteStatus === true) {
            Loader::getInstance()->chatMute[$player->getName()] = true;
        } else {
            return;
        }

        Scoreboard::send($player);
        $msg = str_replace("{player}", $player->getName(), Loader::getInstance()->getConfig()->get('join-message') ?? "");;
        $e->setJoinMessage($msg);
    }

    public function handleQuit(PlayerQuitEvent $e): void
    {
        $player = $e->getPlayer();

        $msg = str_replace("{player}", $player->getName(), Loader::getInstance()->getConfig()->get('quit-message') ?? "");;
        $e->setQuitMessage($msg);
    }

    public function handleChat(PlayerChatEvent $e): void
    {
        $player = $e->getPlayer();

        if (Loader::getInstance()->chatMuteStatus === true) {
            $e->cancel();
        }
    }

    public function handleJump(PlayerJumpEvent $e): void
    {
        $player = $e->getPlayer();
        $jump = [];

        if ($jump[$player->getName()] < 1) {
            $jump[$player->getName()] = $jump[$player->getName()] + 1;;
        } elseif ($jump[$player->getName()] >= 2) {
            $direction = $player->getDirectionVector()->multiply(1.2);
            $player->setMotion($direction);
            unset($jump[$player->getName()]);
        }
    }

    public function handleInteract(PlayerInteractEvent $e): void
    {
        $player = $e->getPlayer();
        $item = $e->getItem();

        if (!$item->getNamedTag()->getTag("item")) return;

        $tag = $item->getNamedTag()->getString("item");

        if ($tag === "enderpearl") {
            $direction = $player->getDirectionVector()->multiply(1.2);
            $player->setMotion($direction);
        } elseif ($tag === "selector") {
            Menu::send($player);
        }
    }

    public function giveItems(Player $player): void
    {
        $enderPearl = VanillaItems::ENDER_PEARL();
        $enderPearl->setCustomName("§3EnderPearl");
        $enderPearl->getNamedTag()->setString("item", "enderpearl");

        $selector = VanillaItems::COMPASS();
        $selector->setCustomName("§gSelector");
        $selector->getNamedTag()->setString("item", "selector");

        $player->getInventory()->setItem(5, $selector);
        $player->getInventory()->setItem(3, $enderPearl);
    }

}