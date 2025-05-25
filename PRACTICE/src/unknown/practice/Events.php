<?php

namespace unknown\practice;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use unknown\practice\Queue\QueueManager;
use unknown\practice\scoreboard\Scoreboard;

class Events implements Listener
{
    public function handleJoin(PlayerJoinEvent $e): void
    {
        $player = $e->getPlayer();

        Scoreboard::send($player);
        $msg = str_replace("{player}", $player->getName(), Loader::getInstance()->getConfig()->get('join-message') ?? "");;
        $e->setJoinMessage($msg);
    }

    public function handleQuit(PlayerQuitEvent $e): void
    {
        $player = $e->getPlayer();

        $msg = str_replace("{player}", $player->getName(), Loader::getInstance()->getConfig()->get('quit-message') ?? "");;
        $e->setQuitMessage($msg);
        Loader::getInstance()->getScoreboardManager()->remove($player);
    }

    public function handleChat(PlayerChatEvent $e): void
    {
        $player = $e->getPlayer();

        if (Loader::getInstance()->chatMuteStatus === true) {
            $e->cancel();
        }
    }
}