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

        $hcfQuery = new QueryStatus($config->getNested('servers.hcf.ip'), $config->getNested('servers.hcf.port'));
        $kitmapQuery = new QueryStatus($config->getNested('servers.kitmap.ip'), $config->getNested('servers.kitmap.port'));
        $practiceQuery = new QueryStatus($config->getNested('servers.practice.ip'), $config->getNested('servers.practice.port'));

        $ipText = $ipAnimations[self::$tick % count($ipAnimations)];

        $hcf = $hcfQuery->query()['status'] === "On" ? "&7" . $hcfQuery->query()['players_online'] . "/" . $hcfQuery->query()['max_players'] : "&cOffline";
        $kitmap = $kitmapQuery->query()['status'] === "On" ? "&7" . $kitmapQuery->query()['players_online'] . "/" . $kitmapQuery->query()['max_players'] : "&cOffline";
        $practice = $practiceQuery->query()['status'] === "On" ? "&7" . $practiceQuery->query()['players_online'] . "/" . $practiceQuery->query()['max_players'] : "&cOffline";

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