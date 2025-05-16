<?php

namespace unknown\scoreboard;

use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use unknown\Loader;

class Scoreboard {

    private static int $tick = 0;

    public static function send(Player $player): void {
        $config = Loader::getInstance()->getConfig();

        $ipAnimations = $config->get('scoreboard')['stri'] ?? ["play.hub.sytes"];

        $hcfstatus = Loader::getInstance()->getQueryManager()->getStatus('hcf');
        $kitmapstatus = Loader::getInstance()->getQueryManager()->getStatus('kitmap');
        $practicestatus = Loader::getInstance()->getQueryManager()->getStatus('practice');

        $ipText = $ipAnimations[self::$tick % count($ipAnimations)];

        $hcf = $hcfstatus !== null ? "&7" . $hcfstatus['online'] . "/" . $hcfstatus['max'] : "&cOffline";
        $kitmap = $kitmapstatus !== null ? "&7" . $kitmapstatus['online'] . "/" . $kitmapstatus['max'] : "&cOffline";
        $practice = $practicestatus !== null ? "&7" . $practicestatus['online'] . "/" . $practicestatus['max'] : "&cOffline";
        $rank = Loader::getInstance()->getRankManage()->getRank($player->getName());

        $lines = [];

        $lines[] = "&e&m---------------------";
        $lines[] = "&l&gRank: &a" . $rank;
        $lines[] = "&e&r";

        $lines[] = "&l&gPing: &r&a" . $player->getNetworkSession()->getPing() . "ms";
        $lines[] = "&l&gOnline: &r&b" . count(Server::getInstance()->getOnlinePlayers());
        $lines[] = "&e&r";
        $lines[] = "      &l&gStatus";
        $lines[] = "&lHCF&7: &r&7" . $hcf;
        $lines[] = "&lKITMAP&7: " . $kitmap;
        $lines[] = "&lPRACTICE&7: " . $practice;
        $lines[] = "&e&m--------" . $ipText . "--------";

        $coloredLines = [];
        foreach ($lines as $line) {
            $coloredLines[] = TextFormat::colorize($line);
        }

        Loader::getInstance()->getScoreboardManager()->setLines($player, $coloredLines);
    }

    public static function nextTick(): void {
        self::$tick++;
    }
}
