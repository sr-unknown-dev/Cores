<?php

namespace hcf\module\ranksystem;

use hcf\databases\RanksDatabase;
use hcf\Loader;
use hcf\module\ranksystem\commands\RankCommands;
use hcf\player\Player;
use pocketmine\utils\Config;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\TextFormat;

class RankManager
{
    /**
     * @var Config
     */
    public Config $ranks;
    private array $attachments = [];
    private RanksDatabase $database;

    public $diagram;

    public function __construct() {
        $this->database = RanksDatabase::getInstance();
        $this->ranks = new Config(Loader::getInstance()->getDataFolder() . "ranks.yml", Config::YAML, [
            "default" => "Guest",
            "Guest" => [
                "format" => "&l&aGeneral",
                "chatFormat" => "&8[&l&aGeneral&8] &r&f{player}: &7{message}",
                "permissions" => []
            ]
        ]);

        $this->database->getConnection()->query("CREATE TABLE IF NOT EXISTS player_ranks (
            player_name VARCHAR(32) PRIMARY KEY,
            rank_name VARCHAR(32),
            expiration_time INT DEFAULT 0
        )");

        $commandMap = Loader::getInstance()->getServer()->getCommandMap();
        $commandMap->register("ranks", new RankCommands("ranks", "Ranks Command"));
        Loader::getInstance()->getServer()->getPluginManager()->registerEvents(new RanksListener(), Loader::getInstance());
    }
    /**
     * @param Player $s
     * @param string $name
     * @param string $format
     * @return void
     */
    public function createRank(Player $s, string $name, string $format) {
        $name = trim($name);
        if ($this->ranks->exists($name)) {
            $s->sendMessage(TextFormat::colorize("&8[&6Ranks&8] &aEl rank {$name} ya está creado"));
            return;
        }
        $data = [
            "format" => $format,
            "chatFormat" => "&8[{$format}&r&8] &r&f{player}: &7{message}",
            "permissions" => []
        ];
        $this->ranks->setNested($name, $data);
        $this->ranks->save();
        $s->sendMessage(TextFormat::colorize("&8[&6Ranks&8] &aEl rank {$name} ha sido creado con éxito"));
    }

    /**
     * @param Player $s
     * @param string $name
     * @return void
     */
    public function deleteRank(Player $s, string $name) {
        if (!$this->ranks->exists($name)) {
            $s->sendMessage(TextFormat::colorize("&8[&6Ranks&8] &aEl rank {$name} no existe"));
            return;
        }
        $this->ranks->removeNested($name);
        $this->ranks->save();
        $s->sendMessage(TextFormat::colorize("&8[&6Ranks&8] &aEl rank {$name} ha sido eliminado con éxito"));
    }

    /**
     * @param Player $player
     * @param string $rank
     * @param int $duration
     * @return void
     */
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

    /**
     * @param Player $player
     * @return string
     */
    public function getPlayerRank(Player $player): string {
        $conn = $this->database->getConnection();
        $playerName = $player->getName();

        $stmt = $conn->prepare("SELECT rank_name FROM player_ranks WHERE player_name = ?");
        $stmt->bind_param("s", $playerName);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows > 0) {
            return $result->fetch_assoc()["rank_name"];
        }

        return $this->ranks->get("default");
    }

    /**
     * @param Player $player
     * @return void
     */
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

    /**
     * @param Player $player
     * @return void
     */
    private function removePlayerAttachments(Player $player) {
        if (isset($this->attachments[$player->getName()])) {
            foreach ($this->attachments[$player->getName()] as $attachment) {
                $player->removeAttachment($attachment);
            }
            unset($this->attachments[$player->getName()]);
        }
    }

    /**
     * @param Player $player
     * @return void
     */
    public function removePlayerRank(Player $player): void {
        $conn = $this->database->getConnection();
        $playerName = $player->getName();

        $stmt = $conn->prepare("DELETE FROM player_ranks WHERE player_name = ?");
        $stmt->bind_param("s", $playerName);
        $stmt->execute();

        $this->applyPermissions($player);
    }

    /**
     * @param string $rank
     * @return array
     */
    public function getRankPermissions(string $rank): array {
        return $this->ranks->get($rank)["permissions"] ?? [];
    }

    /**
     * @param string $rank
     * @return string
     */
    public function getChatFormat(string $rank): string {
        return $this->ranks->get($rank)["chatFormat"] ?? '&8[&aGuest&8]&r &a{player}&f: &7{message}';
    }

    /**
     * @param string $rank
     * @return bool
     */
    public function isExist(string $rank): bool {
        return $this->ranks->exists($rank);
    }

    /**
     * @param Player $player
     * @return void
     */
    public function RankList(Player $player) {
        $ranks = $this->ranks->getAll();
        $player->sendMessage("Rango por defecto: " . $ranks['default']);
        foreach ($ranks as $rank => $details) {
            if ($rank !== "default") {
                $player->sendMessage($rank."\n");
            }
        }
    }

    /**
     * @param Player $s
     * @param Player $u
     * @return void
     */
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

            if($expirationTime > 0) {
                $remainingTime = $expirationTime - time();
                if($remainingTime > 0) {
                    $message .= "§7Expiración: §6" . $this->formatTime($remainingTime) . "\n";
                } else {
                    $message .= "§7Expiración: §cYa expiró\n";
                }
            } else {
                $message .= "§7Expiración: §6Permanente\n";
            }
        } else {
            $message .= "§7Expiración: §6Permanente\n";
        }

        $s->sendMessage(TextFormat::colorize($message));
    }

    /**
     * @return void
     */
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

    public function getAll(){
        return $this->ranks->getAll();
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

    /**
     * @param string $durationStr
     * @return int|null
     */
    public function parseDuration(string $durationStr): ?int {
        $totalSeconds = 0;
        if (preg_match_all('/(\d+)([mnd])/', $durationStr, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $value = (int)$match[1];
                $unit = $match[2];
                switch ($unit) {
                    case 'm':
                        $totalSeconds += $value * 60;
                        break;
                    case 'd':
                        $totalSeconds += $value * 86400;
                        break;
                    case 'n':
                        $totalSeconds += $value * 2592000;
                        break;
                    default:
                        return null;
                }
            }
            return $totalSeconds;
        }
        return null;
    }

    /**
     * @return mixed
     */
    public function getDiagram()
    {
        return $this->diagram;
    }

    /**
     * @param mixed $diagram
     */
    public function setDiagram($diagram): void
    {
        $this->diagram = $diagram;
    }
}