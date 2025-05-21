<?php

namespace hcf\module\ranksystem;

use hcf\databases\RanksDatabase;
use hcf\Loader;
use hcf\module\ranksystem\commands\RankCommands;
use hcf\player\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\TextFormat;

class RankManager {
    private array $attachments = [];
    private RanksDatabase $database;
    public $diagram;

    public function __construct() {
        $this->database = RanksDatabase::getInstance();
        $this->initTables();

        Loader::getInstance()->getServer()->getCommandMap()->register("ranks", new RankCommands("ranks", "Ranks Command"));
        Loader::getInstance()->getServer()->getPluginManager()->registerEvents(new RanksListener(), Loader::getInstance());
    }

    private function initTables(): void {
        $conn = $this->database->getConnection();
        $conn->query("CREATE TABLE IF NOT EXISTS player_ranks (
            player_name VARCHAR(32) PRIMARY KEY,
            rank_name VARCHAR(32),
            expiration_time INT DEFAULT 0
        )");

        $conn->query("CREATE TABLE IF NOT EXISTS ranks (
            rank_name VARCHAR(32) PRIMARY KEY,
            format VARCHAR(255),
            chat_format TEXT,
            permissions TEXT
        )");

        $conn->query("INSERT IGNORE INTO ranks (rank_name, format, chat_format, permissions) VALUES (
            'Guest',
            '&l&aGeneral',
            '&8[&l&aGeneral&8] &r&f{player}: &7{message}',
            ''
        )");
    }

    public function createRank(Player $s, string $name, string $format): void {
        $name = trim($name);
        if ($this->isExist($name)) {
            $s->sendMessage(TextFormat::colorize("&8[&6Ranks&8] &aEl rank {$name} ya está creado"));
            return;
        }

        $chatFormat = "&8[{$format}&r&8] &r&f{player}: &7{message}";
        $stmt = $this->database->getConnection()->prepare("INSERT INTO ranks (rank_name, format, chat_format, permissions) VALUES (?, ?, ?, ?)");
        $emptyPermissions = json_encode([]);
        $stmt->bind_param("ssss", $name, $format, $chatFormat, $emptyPermissions);
        $stmt->execute();

        $s->sendMessage(TextFormat::colorize("&8[&6Ranks&8] &aEl rank {$name} ha sido creado con éxito"));
    }

    public function deleteRank(Player $s, string $name): void {
        if (!$this->isExist($name)) {
            $s->sendMessage(TextFormat::colorize("&8[&6Ranks&8] &aEl rank {$name} no existe"));
            return;
        }

        $stmt = $this->database->getConnection()->prepare("DELETE FROM ranks WHERE rank_name = ?");
        $stmt->bind_param("s", $name);
        $stmt->execute();

        $s->sendMessage(TextFormat::colorize("&8[&6Ranks&8] &aEl rank {$name} ha sido eliminado con éxito"));
    }

    public function setPlayerRank(Player $player, string $rank, int $duration = 0): void {
        $conn = $this->database->getConnection();
        $playerName = $player->getName();
        $expirationTime = $duration > 0 ? time() + $duration : 0;

        $stmt = $conn->prepare("REPLACE INTO player_ranks (player_name, rank_name, expiration_time) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $playerName, $rank, $expirationTime);
        $stmt->execute();

        $this->applyPermissions($player);

        if ($duration > 0) {
            Loader::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(
                function() use ($player) {
                    $this->removePlayerRank($player);
                    $player->sendMessage(TextFormat::colorize("&8[&6Ranks&8] &cTu rank ha expirado"));
                }
            ), $duration * 20);
        }
    }

    public function getPlayerRank(Player $player): string {
        $conn = $this->database->getConnection();
        $stmt = $conn->prepare("SELECT rank_name FROM player_ranks WHERE player_name = ?");
        $playerName = $player->getName();
        $stmt->bind_param("s", $playerName);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows > 0) {
            return $result->fetch_assoc()["rank_name"];
        }

        return "Guest";
    }

    public function applyPermissions(Player $player): void {
        if(!$player instanceof Player) return;
        $this->removePlayerAttachments($player);
        $rank = $this->getPlayerRank($player);
        $permissions = $this->getRankPermissions($rank);
        foreach ($permissions as $permission) {
            $attachment = $player->addAttachment(Loader::getInstance(), $permission, true);
            $this->attachments[$player->getName()][] = $attachment;
        }
    }

    private function removePlayerAttachments(Player $player): void {
        if (isset($this->attachments[$player->getName()])) {
            foreach ($this->attachments[$player->getName()] as $attachment) {
                $player->removeAttachment($attachment);
            }
            unset($this->attachments[$player->getName()]);
        }
    }

    public function removePlayerRank(Player $player): void {
        $stmt = $this->database->getConnection()->prepare("DELETE FROM player_ranks WHERE player_name = ?");
        $playerName = $player->getName();
        $stmt->bind_param("s", $playerName);
        $stmt->execute();

        $this->applyPermissions($player);
    }

    public function getRankPermissions(string $rank): array {
        $stmt = $this->database->getConnection()->prepare("SELECT permissions FROM ranks WHERE rank_name = ?");
        $stmt->bind_param("s", $rank);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            return json_decode($row['permissions'], true) ?? [];
        }
        return [];
    }

    public function getChatFormat(string $rank): string {
        $stmt = $this->database->getConnection()->prepare("SELECT chat_format FROM ranks WHERE rank_name = ?");
        $stmt->bind_param("s", $rank);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            return $row["chat_format"];
        }
        return '&8[&aGuest&8]&r &a{player}&f: &7{message}';
    }

    public function isExist(string $rank): bool {
        $stmt = $this->database->getConnection()->prepare("SELECT rank_name FROM ranks WHERE rank_name = ?");
        $stmt->bind_param("s", $rank);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    public function RankList(Player $player): void {
        $res = $this->database->getConnection()->query("SELECT rank_name FROM ranks");
        $player->sendMessage("§7Rangos disponibles:");
        while ($row = $res->fetch_assoc()) {
            $player->sendMessage("§8- §f" . $row["rank_name"]);
        }
    }

    public function userInfo(Player $s, Player $u): void {
        $conn = $this->database->getConnection();
        $playerName = $u->getName();

        $stmt = $conn->prepare("SELECT rank_name, expiration_time FROM player_ranks WHERE player_name = ?");
        $stmt->bind_param("s", $playerName);
        $stmt->execute();
        $result = $stmt->get_result();

        $rank = $this->getPlayerRank($u);
        $message = "§7Player Information §6" . $playerName . ":\n";
        $message .= "§7Rank§6: " . $rank . "\n";

        if($result->num_rows > 0) {
            $data = $result->fetch_assoc();
            $expirationTime = $data["expiration_time"];
            $message .= "§7Expiración: §6" . ($expirationTime > 0 ? $this->formatTime($expirationTime - time()) : "Permanente") . "\n";
        } else {
            $message .= "§7Expiración: §6Permanente\n";
        }

        $s->sendMessage(TextFormat::colorize($message));
    }

    public function checkExpiredRanks(): void {
        $conn = $this->database->getConnection();
        $currentTime = time();

        $stmt = $conn->prepare("SELECT player_name FROM player_ranks WHERE expiration_time > 0 AND expiration_time <= ?");
        $stmt->bind_param("i", $currentTime);
        $stmt->execute();
        $result = $stmt->get_result();

        while($row = $result->fetch_assoc()) {
            $player = Loader::getInstance()->getServer()->getPlayerExact($row["player_name"]);
            if($player !== null) {
                $this->removePlayerRank($player);
                $player->sendMessage(TextFormat::colorize("&8[&6Ranks&8] &cTu rank ha expirado"));
            }
        }

        $conn->query("DELETE FROM player_ranks WHERE expiration_time > 0 AND expiration_time <= $currentTime");
    }

    public function getAll(): array {
        $result = $this->database->getConnection()->query("SELECT * FROM ranks");
        $ranks = [];
        while ($row = $result->fetch_assoc()) {
            $ranks[$row["rank_name"]] = $row;
        }
        return $ranks;
    }

    private function formatTime(int $seconds): string {
        $days = floor($seconds / 86400);
        $hours = floor(($seconds % 86400) / 3600);
        $minutes = floor(($seconds % 3600) / 60);

        $parts = [];
        if($days > 0) $parts[] = $days . "d";
        if($hours > 0) $parts[] = $hours . "h";
        if($minutes > 0) $parts[] = $minutes . "m";

        return empty($parts) ? "menos de 1m" : implode(" ", $parts);
    }

    public function parseDuration(string $durationStr): ?int {
        $totalSeconds = 0;
        if (preg_match_all('/(\d+)([mnd])/', $durationStr, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $value = (int)$match[1];
                $unit = $match[2];
                switch ($unit) {
                    case 'm': $totalSeconds += $value * 60; break;
                    case 'd': $totalSeconds += $value * 86400; break;
                    case 'n': $totalSeconds += $value * 2592000; break;
                    default: return null;
                }
            }
            return $totalSeconds;
        }
        return null;
    }
}