<?php

namespace unknown\scoreboard;

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

        $hcfQuery = new QueryStatus($config->getNested('servers.hcf.ip'), $config->getNested('servers.hcf.port'));
        $kitmapQuery = new QueryStatus($config->getNested('servers.kitmap.ip'), $config->getNested('servers.kitmap.port'));
        $practiceQuery = new QueryStatus($config->getNested('servers.practice.ip'), $config->getNested('servers.practice.port'));

        $hcfQuery->setCacheTime(30);
        $kitmapQuery->setCacheTime(30);
        $practiceQuery->setCacheTime(30);

        $hcfData = $hcfQuery->query();
        $kitmapData = $kitmapQuery->query();
        $practiceData = $practiceQuery->query();

        $hcfStatus = $hcfData['status'] ?? "Off";
        $hcfOnline = $hcfData['players_online'] ?? 0;
        $hcfMax = $hcfData['max_players'] ?? 0;

        $kitmapStatus = $kitmapData['status'] ?? "Off";
        $kitmapOnline = $kitmapData['players_online'] ?? 0;
        $kitmapMax = $kitmapData['max_players'] ?? 0;

        $practiceStatus = $practiceData['status'] ?? "Off";
        $practiceOnline = $practiceData['players_online'] ?? 0;
        $practiceMax = $practiceData['max_players'] ?? 0;

        $ipText = $ipAnimations[self::$tick % count($ipAnimations)];

        $hcf = ($hcfStatus === "On") ? "&7{$hcfOnline}/{$hcfMax}" : "&cOffline";
        $kitmap = ($kitmapStatus === "On") ? "&7{$kitmapOnline}/{$kitmapMax}" : "&cOffline";
        $practice = ($practiceStatus === "On") ? "&7{$practiceOnline}/{$practiceMax}" : "&cOffline";

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

    public static function nextTick(): void
    {
        self::$tick++;
    }
}