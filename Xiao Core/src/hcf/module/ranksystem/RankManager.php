<?php

namespace hcf\module\ranksystem;

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
    public Config $playerRanks;
    public Config $rankExpirations;
    private array $attachments = [];

    /**
     *
     */
    public function __construct() {
        $this->ranks = new Config(Loader::getInstance()->getDataFolder() . "ranks.yml", Config::YAML, [
            "default" => "Guest",
            "Guest" => [
                "format" => "&l&aGeneral",
                "chatFormat" => "&8[&c{faction}&8]&r {prefix} &8[&l&aGeneral&8] &r&f{player}: &7{message}",
                "permissions" => []
            ]
        ]);
        $this->playerRanks = new Config(Loader::getInstance()->getDataFolder() . "player_ranks.yml", Config::YAML);
        $this->rankExpirations = new Config(Loader::getInstance()->getDataFolder() . "rank_expirations.yml", Config::YAML);
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
            "chatFormat" => "&8[&c{faction}&8]&r {prefix} &8[{$format}&r&8] &r&f{player}: &7{message}",
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
    public function setPlayerRank(Player $player, string $rank, int $duration = 0) {
        $this->playerRanks->set($player->getName(), $rank);
        $this->playerRanks->save();
        $this->applyPermissions($player);
        if ($duration > 0) {
            $expirationTime = time() + $duration;
            $this->rankExpirations->set($player->getName(), $expirationTime);
            $this->rankExpirations->save();
            Loader::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function() use ($player) {
                $this->removePlayerRank($player);
                $player->sendMessage(TextFormat::colorize("&8[&6Ranks&8] &cTu rank ha expirado"));
            }), $duration * 20);
        }
    }

    /**
     * @param Player $player
     * @return string
     */
    public function getPlayerRank(Player $player): string {
        return $this->playerRanks->get($player->getName(), $this->ranks->get("default"));
    }

    /**
     * @param Player $player
     * @return void
     */
    public function applyPermissions(Player $player) {
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
    public function removePlayerRank(Player $player) {
        $this->playerRanks->remove($player->getName());
        $this->playerRanks->save();
        $this->rankExpirations->remove($player->getName());
        $this->rankExpirations->save();
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
        return $this->ranks->get($rank)["chatFormat"] ?? '&8[&c{faction}&8]&r {prefix} &8[&aGuest&8]&r &a{player}&f: &7{message}';
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
    public function userInfo(Player $s, Player $u) {
        $rank = $this->getPlayerRank($u);
        $expirationTime = $this->rankExpirations->get($u->getName(), null);
        $message = "§7Player Information §6 " . $u->getName() . ":\n";
        $message .= "§7Rank§6: " . $rank . "\n";
        if ($expirationTime !== null) {
            $remainingTime = $expirationTime - time();
            if ($remainingTime > 0) {
                $message .= "§7Expiratión: §6" . $this->parseDuration($remainingTime) . "\n";
            } else {
                $message .= "Its has already expire.\n";
            }
        } else {
            $message .= "§7Expiratión: §6Permanente.\n";
        }
        $s->sendMessage(TextFormat::colorize($message));
    }

    /**
     * @return void
     */
    public function checkExpiredRanks() {
        $currentTime = time();
        foreach ($this->rankExpirations->getAll() as $playerName => $expirationTime) {
            if ($currentTime >= $expirationTime) {
                $player = Loader::getInstance()->getServer()->getPlayerExact($playerName);
                if ($player !== null) {
                    $this->removePlayerRank($player);
                } else {
                    $this->removeRankData($playerName);
                }
            }
        }
    }

    /**
     * @param string $playerName
     * @return void
     */
    private function removeRankData(string $playerName) {
        $this->playerRanks->remove($playerName);
        $this->playerRanks->save();
        $this->rankExpirations->remove($playerName);
        $this->rankExpirations->save();
    }

    public function getAll(){
        return $this->ranks->getAll();
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
}