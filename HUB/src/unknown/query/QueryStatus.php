<?php

declare(strict_types=1);

namespace unknown\query;

use jasonw4331\libpmquery\PMQuery;
use jasonw4331\libpmquery\PmQueryException;
use pocketmine\scheduler\ClosureTask;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use unknown\Loader;

class QueryStatus
{

    public static Config $config;

    public function __construct()
    {
        self::$config = Loader::getInstance()->getConfig();
    }

    public static function infoHCF(): array
    {
        try {
            $query = PMQuery::query(self::$config->getNested('servers.hcf.ip'), self::$config->getNested('servers.hcf.port'));
            $players = (int)$query['Players'];
            $maxPlayers = (int)$query['MaxPlayers'];

            return [
                "players" => TextFormat::GRAY . $players . "/" . $maxPlayers,
                "status" => TextFormat::GREEN.$query['Status'],
            ];

        } catch (PmQueryException $e) {
            return [
                "players" => TextFormat::GRAY . "0/0",
                "status" => "§4Offline"
            ];
        }
    }

    public static function infoKitMap(): array
    {
        try {
            $query = PMQuery::query(self::$config->getNested('servers.kitmap.ip'), self::$config->getNested('servers.kitmap.port'));
            $players = (int)$query['Players'];
            $maxPlayers = (int)$query['MaxPlayers'];

            return [
                "players" => TextFormat::GRAY . $players . "/" . $maxPlayers,
                "status" => TextFormat::GREEN.$query['Status'],
            ];

        } catch (PmQueryException $e) {
            return [
                "players" => TextFormat::GRAY . "0/0",
                "status" => "§4Offline"
            ];
        }
    }

    public static function infoPractice(): array
    {
        try {
            $query = PMQuery::query(self::$config->getNested('servers.practice.ip'), self::$config->getNested('servers.practice.port'));
            $players = (int)$query['Players'];
            $maxPlayers = (int)$query['MaxPlayers'];

            return [
                "players" => TextFormat::GRAY . $players . "/" . $maxPlayers,
                "status" => TextFormat::GREEN.$query['Status'],
            ];
        } catch (PmQueryException $e) {
            return [
                "players" => TextFormat::GRAY . "0/0",
                "status" => "§4Offline"
            ];
        }
    }
}