<?php

namespace unknown\practice\Queue;

use pocketmine\Server;
use unknown\practice\Match\MatchManager;

class QueueManager
{
    /** @var Queue[] */
    private array $queue = [];

    private static ?QueueManager $instance = null;

    public static function getInstance(): QueueManager
    {
        return self::$instance ??= new self();
    }

    public function addQueue(Queue $queue): void
    {
        $this->queue[] = $queue;
        $this->attemptMatch($queue);
    }

    public function removeQueue(Queue $queue): void
    {
        unset($this->queue[array_search($queue, $this->queue, true)]);
    }

    public function getQueue(): array
    {
        return $this->queue;
    }

    public function getPlayerQueue(string $playerName): ?Queue
    {
        foreach ($this->queue as $queue) {
            if (strtolower($queue->getOwner()) === strtolower($playerName)) {
                return $queue;
            }
        }
        return null;
    }

    private function attemptMatch(Queue $newQueue): void
    {
        foreach ($this->queue as $existingQueue) {
            if ($existingQueue === $newQueue || $existingQueue->getState() !== "PENDING") continue;

            if (
                $existingQueue->getMode() === $newQueue->getMode() &&
                $existingQueue->getType() === $newQueue->getType()
            ) {
                // Match found
                $existingQueue->setState("PAIRED");
                $newQueue->setState("PAIRED");

                MatchManager::getInstance()->createGame($existingQueue, $newQueue);
                return;
            }
        }
    }
}