<?php

namespace unknown\utils;

use mysqli;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use unknown\Loader;

class RankManager {
    private ?mysqli $connection = null;

    public function __construct() {

        try {
            $config =Loader::getInstance()->getConfig();

            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
            $this->connection = new mysqli(
                $config->getNested("database.host"),
                $config->getNested("database.username"),
                $config->getNested("database.password"),
                $config->getNested("database.database"),
                $config->getNested("database.port")
            );

            $this->connection->set_charset("utf8mb4");
        } catch (\Throwable $e) {
            Server::getInstance()->getLogger()->error("Error conectando con la base de datos de 'hcf': " . $e->getMessage());
        }
    }

    public function getRank(string $playerName): string {
        if ($this->connection === null) {
            return TextFormat::colorize("&7Jugador");
        }

        try {
            $stmt = $this->connection->prepare("SELECT rank_name FROM player_ranks WHERE player_name = ?");
            $stmt->bind_param("s", $playerName);
            $stmt->execute();
            $stmt->bind_result($rank);
            if ($stmt->fetch()) {
                $stmt->close();
                return TextFormat::colorize($this->formatRank($rank));
            }
            $stmt->close();
        } catch (\Throwable $e) {
            Server::getInstance()->getLogger()->warning("Error al obtener el rango de $playerName: " . $e->getMessage());
        }

        return TextFormat::colorize("&7Jugador");
    }

    private function formatRank(string $rank): string {
        return match(strtolower($rank)) {
            "founder" => "&cAdmin",
            "owner" => "&2Mod",
            "manager" => "&4Owner",
            "" => "&9Builder",
            "youtube" => "&cYou&fTube",
            "vip" => "&6VIP",
            "default", "jugador" => "&7Jugador",
            default => "&f" . ucfirst($rank)
        };
    }
}
