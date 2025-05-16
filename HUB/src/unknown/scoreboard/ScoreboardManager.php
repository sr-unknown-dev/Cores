<?php

namespace unknown\scoreboard;

use pocketmine\network\mcpe\protocol\{RemoveObjectivePacket,
    SetDisplayObjectivePacket,
    SetScorePacket,
    types\ScorePacketEntry};
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use unknown\Loader;

class ScoreboardManager {

    private array $lines = [];
    private array $titles = [];

    public function create(Player $player, string $title): void {
        $this->remove($player);

        $this->titles[$player->getName()] = $title;
        $this->lines[$player->getName()] = [];

        $packet = SetDisplayObjectivePacket::create(
            "sidebar",
            "scoreboard",
            $title,
            "dummy",
            0
        );
        $player->getNetworkSession()->sendDataPacket($packet);
    }

    public function setLines(Player $player, array $lines): void {
        $name = $player->getName();

        if (!isset($this->titles[$name])) {
            $hubAnimations = Loader::getInstance()->getConfig()->get('scoreboard')['title'] ?? ["&aHUB"];
            $hubText = $hubAnimations[self::$tick % count($hubAnimations)];
            $this->create($player, $hubText);
        }

        $this->lines[$name] = [];

        $score = 0;
        foreach ($lines as $line) {
            $this->lines[$name][$score] = TextFormat::colorize($line);
            $score++;
        }

        $this->sendLines($player);
    }

    public function remove(Player $player): void {
        $packet = RemoveObjectivePacket::create("scoreboard");
        $player->getNetworkSession()->sendDataPacket($packet);
        unset($this->lines[$player->getName()], $this->titles[$player->getName()]);
    }

    private function sendLines(Player $player): void {
        $name = $player->getName();
        if (!isset($this->lines[$name])) return;

        $packet = new SetScorePacket();
        $packet->type = SetScorePacket::TYPE_CHANGE;

        foreach ($this->lines[$name] as $score => $text) {
            $entry = new ScorePacketEntry();
            $entry->objectiveName = "scoreboard";
            $entry->type = ScorePacketEntry::TYPE_FAKE_PLAYER;
            $entry->score = $score;
            $entry->scoreboardId = $score;
            $entry->customName = $text;
            $packet->entries[] = $entry;
        }

        $player->getNetworkSession()->sendDataPacket($packet);
    }
}