<?php

namespace unknown\practice\Match;

use unknown\practice\Queue\Queue;
use pocketmine\player\Player;
use pocketmine\Server;

class Game
{
    private Queue $queue1;
    private Queue $queue2;

    public function __construct(Queue $queue1, Queue $queue2)
    {
        $this->queue1 = $queue1;
        $this->queue2 = $queue2;

        $this->start();
    }

    private function start(): void
    {
        $player1 = Server::getInstance()->getPlayerExact($this->queue1->getOwner());
        $player2 = Server::getInstance()->getPlayerExact($this->queue2->getOwner());

        if ($player1 === null || $player2 === null) {
            return;
        }

        // Teleport, send message, equip kits, etc
        $player1->teleport($player1->getWorld()->getSafeSpawn());
        $player2->teleport($player2->getWorld()->getSafeSpawn());

        $player1->sendMessage("\n§a[Match] §fHas sido emparejado con §e" . $player2->getName());
        $player2->sendMessage("\n§a[Match] §fHas sido emparejado con §e" . $player1->getName());
    }
}