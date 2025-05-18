<?php

namespace unknown\query;

use jasonw4331\libpmquery\PMQuery;
use jasonw4331\libpmquery\PmQueryException;
use pocketmine\utils\TextFormat;
use unknown\Loader;

class QueryStatus
{
    private static array $cache = [];
    private static int $cacheTime = 3; // segundos

    private static function getCached(string $key, callable $callback): array {
        $now = time();
        if (isset(self::$cache[$key]) && ($now - self::$cache[$key]['time']) < self::$cacheTime) {
            return self::$cache[$key]['data'];
        }
        $data = $callback();
        self::$cache[$key] = [
            'time' => $now,
            'data' => $data
        ];
        return $data;
    }

    public static function infoHCF(): array
    {
        return self::getCached('hcf', function() {
            try {
                $query = PMQuery::query(
                    Loader::getInstance()->getConfig()->getNested('servers.hcf.ip'),
                    Loader::getInstance()->getConfig()->getNested('servers.hcf.port')
                );
                $players = (int)$query['Players'];
                $maxPlayers = (int)$query['MaxPlayers'];
                return [
                    "players" => TextFormat::GRAY . $players . "/" . $maxPlayers,
                    "status" => TextFormat::GREEN."Online",
                ];
            } catch (PmQueryException $e) {
                return [
                    "players" => TextFormat::GRAY . "0/0",
                    "status" => "§4Offline"
                ];
            }
        });
    }

    public static function infoKitMap(): array
    {
        return self::getCached('kitmap', function() {
            try {
                $query = PMQuery::query(
                    Loader::getInstance()->getConfig()->getNested('servers.kitmap.ip'),
                    Loader::getInstance()->getConfig()->getNested('servers.kitmap.port')
                );
                $players = (int)$query['Players'];
                $maxPlayers = (int)$query['MaxPlayers'];
                return [
                    "players" => TextFormat::GRAY . $players . "/" . $maxPlayers,
                    "status" => TextFormat::GREEN."Online",
                ];
            } catch (PmQueryException $e) {
                return [
                    "players" => TextFormat::GRAY . "0/0",
                    "status" => "§4Offline"
                ];
            }
        });
    }

    public static function infoPractice(): array
    {
        return self::getCached('practice', function() {
            try {
                $query = PMQuery::query(
                    Loader::getInstance()->getConfig()->getNested('servers.practice.ip'),
                    Loader::getInstance()->getConfig()->getNested('servers.practice.port')
                );
                $players = (int)$query['Players'];
                $maxPlayers = (int)$query['MaxPlayers'];
                return [
                    "players" => TextFormat::GRAY . $players . "/" . $maxPlayers,
                    "status" => TextFormat::GREEN."Online",
                ];
            } catch (PmQueryException $e) {
                return [
                    "players" => TextFormat::GRAY . "0/0",
                    "status" => "§4Offline"
                ];
            }
        });
    }
}