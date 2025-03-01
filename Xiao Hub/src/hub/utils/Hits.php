<?php

namespace hub\utils;

use hub\player\Player;

class Hits
{
    public $hits = [];

    public function ability(Player $p, $tag): bool
    {
        $name = $p->getName();
        if (isset($this->hits[$name][$tag])) {
            $data = $this->hits[$name][$tag];
            if (count($data) >= 3) {
                $this->hits[$name][$tag] = [];
                return true;
            }
        }
        $this->hits[$name][$tag][] = microtime(true);
        return false;
    }
}