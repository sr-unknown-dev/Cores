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

    public static function infoHCF(): array
    {
        try {
            $query = PMQuery::query(Loader::getInstance()->getConfig()->getNested('servers.hcf.ip'), Loader::getInstance()->getConfig()->getNested('servers.hcf.port'));
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
            $query = PMQuery::query(Loader::getInstance()->getConfig()->getNested('servers.kitmap.ip'), Loader::getInstance()->getConfig()->getNested('servers.kitmap.port'));
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
            $query = PMQuery::query(Loader::getInstance()->getConfig()->getNested('servers.practice.ip'), Loader::getInstance()->getConfig()->getNested('servers.practice.port'));
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