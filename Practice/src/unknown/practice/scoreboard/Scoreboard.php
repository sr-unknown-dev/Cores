<?php

namespace unknown\practice\scoreboard;

use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use unknown\Loader;
use unknown\query\QueryStatus;

class Scoreboard
{

    public static int $tick = 0;

    public static function send(Player $player): void
    {
        $config = Loader::getInstance()->getConfig();

        $ipAnimations = $config->get('scoreboard')['stri'] ?? ["play.hub.sytes"];

        $ipText = $ipAnimations[self::$tick % count($ipAnimations)];

        $rank = Loader::getInstance()->getRankManage()->getRank($player->getName());

        $lines = [];

        $lines[] = "&e&m---------------------";
        $lines[] = "&l&gRank: &a" . $rank;
        $lines[] = "&e&r";

        $lines[] = "&l&gPing: &r&a" . $player->getNetworkSession()->getPing() . "ms";
        $lines[] = "&l&gOnline: &r&b" . count(Server::getInstance()->getOnlinePlayers());
        $lines[] = "&e&r";
        $lines[] = "      &l&gStatus";
        $lines[] = "&lHCF&7: &r" . QueryStatus::infoHCF()['players'];
        $lines[] = "&lKITMAP&7: &r" . QueryStatus::infoKitMap()['players'];
        $lines[] = "&lPRACTICE&7: &r" . QueryStatus::infoKitMap()['players'];;
        $lines[] = "&e&m--------" . $ipText . "--------";

        $coloredLines = [];
        foreach ($lines as $line) {
            $coloredLines[] = TextFormat::colorize($line);
        }

        Loader::getInstance()->getScoreboardManager()->setLines($player, $coloredLines);
    }

    public static function nextTick(): void
    {
        self::$tick++;
    }
}