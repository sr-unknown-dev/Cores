<?php

namespace unknown\rank;

use mysqli;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use unknown\Loader;

class RankManage {
    private ?mysqli $connection = null;

    public function __construct() {
        try {
            $config = Loader::getInstance()->getConfig();

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
            // Obtener el nombre del rango del jugador
            $stmt = $this->connection->prepare("SELECT rank_name FROM player_ranks WHERE player_name = ?");
            $stmt->bind_param("s", $playerName);
            $stmt->execute();
            $stmt->bind_result($rankName);

            if ($stmt->fetch()) {
                $stmt->close();

                // Obtener el formato del rango desde la tabla ranks
                $formatStmt = $this->connection->prepare("SELECT format FROM ranks WHERE rank_name = ?");
                $formatStmt->bind_param("s", $rankName);
                $formatStmt->execute();
                $formatStmt->bind_result($format);

                if ($formatStmt->fetch()) {
                    $formatStmt->close();
                    return TextFormat::colorize($format);
                }

                $formatStmt->close();
            } else {
                $stmt->close();
            }
        } catch (\Throwable $e) {
            Server::getInstance()->getLogger()->warning("Error al obtener el formato del rango de $playerName: " . $e->getMessage());
        }

        return TextFormat::colorize("&7Jugador");
    }
}
