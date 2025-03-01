<?php

declare(strict_types=1);

namespace hcf\faction;

use hcf\Loader;
use hcf\player\Player;

class FactionInvite
{
    
    /**
     * FactionInvite construct.
     * @param Player $player
     * @param string $faction
     * @param int $time
     */
    public function __construct(
        private Player $player,
        private string $faction,
        private int $time
    ) {
    }
    
    /**
     * @return Player
     */
    public function getPlayer(): Player
    {
        return $this->player;
    }
    
    /**
     * @return string
     */
    public function getFaction(): string
    {
        return $this->faction;
    }

    /**
     * @return int
     */
    public function getTime(): int
    {
        return $this->time;
    }

    public function isExpire(): bool
    {
        return $this->time < time();
    }
}