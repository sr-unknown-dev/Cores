<?php

namespace unknown\practice\Match;

use unknown\practice\Queue\Queue;

class MatchManager
{
    /** @var MatchManager|null */
    private static ?MatchManager $instance = null;

    /** @var Game[] */
    private array $games = [];

    public static function getInstance(): MatchManager
    {
        return self::$instance ??= new self();
    }

    public function createGame(Queue $queue1, Queue $queue2): void
    {
        $game = new Game($queue1, $queue2);
        $this->games[] = $game;
    }

    public function getGames(): array
    {
        return $this->games;
    }
}
