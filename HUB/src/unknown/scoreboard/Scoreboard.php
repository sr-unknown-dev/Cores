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

        $hubAnimations = $config->get('scoreboard')['title'] ?? ["&aHUB"];
        $ipAnimations = $config->get('scoreboard')['stri'] ?? ["play.hub.sytes"];

        $hcfstatus = Loader::getInstance()->getQueryManager()->getStatus('hcf');
        $kitmapstatus = Loader::getInstance()->getQueryManager()->getStatus('kitmap');
        $practicestatus = Loader::getInstance()->getQueryManager()->getStatus('practice');

        $hubText = $hubAnimations[self::$tick % count($hubAnimations)];
        $ipText = $ipAnimations[self::$tick % count($ipAnimations)];

        $hcf = $hcfstatus !== null ? "&7" . $hcfstatus['online'] . "/" . $hcfstatus['max'] : "&cOffline";
        $kitmap = $kitmapstatus !== null ? "&7" . $kitmapstatus['online'] . "/" . $kitmapstatus['max'] : "&cOffline";
        $practice = $practicestatus !== null ? "&7" . $practicestatus['online'] . "/" . $practicestatus['max'] : "&cOffline";

        $lines = [];

        $lines[] = "      &l" . $hubText;
        $lines[] = "&e&m---------------------";
        $lines[] = "&l&gRank: &aJugador";
        $lines[] = "&e&r";

        $lines[] = "&l&gPing: &a" . $player->getNetworkSession()->getPing() . "ms";
        $lines[] = "&l&gOnline: &b" . count(Server::getInstance()->getOnlinePlayers());
        $lines[] = "&e&r";
        $lines[] = "      &l&gStatus";
        $lines[] = "&lHCF&7: " . $hcf;
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
