<?php

namespace unknown\scoreboard;

use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use unknown\Loader;
use unknown\query\QueryStatus;

class Scoreboard {

    public static int $tick = 0;

    public static function send(Player $player): void {
        $config = Loader::getInstance()->getConfig();

        $ipAnimations = $config->get('scoreboard')['stri'] ?? ["play.hub.sytes"];

        $queryhcf = new QueryStatus($config->getNested("server.hcf.ip"), $config->getNested("server.hcf.port"));
        $hcfstatus = $queryhcf->getStatus();

        $querykitmap = new QueryStatus($config->getNested("server.kitmap.ip"), $config->getNested("server.kitmap.port"));
        $kitmapstatus = $querykitmap->getStatus();

        $querypractice = new QueryStatus($config->getNested("server.practice.ip"), $config->getNested("server.practice.port"));
        $practicestatus = $querypractice->getStatus();

        $ipText = $ipAnimations[self::$tick % count($ipAnimations)];

        $hcf = $hcfstatus['status'] === 'online' ? "&7" . $hcfstatus['players_online'] . "/" . $hcfstatus['max_players'] : "&cOffline";
        $kitmap = $kitmapstatus['status'] === 'online' ? "&7" . $kitmapstatus['players_online'] . "/" . $kitmapstatus['max_players'] : "&cOffline";
        $practice = $practicestatus['status'] === 'online' ? "&7" . $practicestatus['players_online'] . "/" . $practicestatus['max_players'] : "&cOffline";

        $rank = Loader::getInstance()->getRankManage()->getRank($player->getName());

        $lines = [];

        $lines[] = "&e&m---------------------";
        $lines[] = "&l&gRank: &a" . $rank;
        $lines[] = "&e&r";

        $lines[] = "&l&gPing: &r&a" . $player->getNetworkSession()->getPing() . "ms";
        $lines[] = "&l&gOnline: &r&b" . count(Server::getInstance()->getOnlinePlayers());
        $lines[] = "&e&r";
        $lines[] = "      &l&gStatus";
        $lines[] = "&lHCF&7: &r" . $hcf;
        $lines[] = "&lKITMAP&7: &r" . $kitmap;
        $lines[] = "&lPRACTICE&7: &r" . $practice;
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