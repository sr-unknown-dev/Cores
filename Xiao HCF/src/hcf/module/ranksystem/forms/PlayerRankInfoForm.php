<?php

namespace hcf\module\ranksystem\forms;

use hcf\databases\Database;
use hcf\Loader;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class PlayerRankInfoForm extends SimpleForm {

    private Player $player;
    private Player $target;

    public function __construct(Player $player, Player $target) {
        $this->player = $player;
        $this->target = $target;

        parent::__construct(function(Player $player, ?int $data) {
            if($data === null) return;
        });

        $rankManager = Loader::getInstance()->getRankManager();
        $conn = Database::getInstance()->getConnection();
        
        // Get rank info from database
        $stmt = $conn->prepare("SELECT rank_name, expiration_time FROM player_ranks WHERE player_name = ?");
        $targetName = $this->target->getName();
        $stmt->bind_param("s", $targetName);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $content = "§7Player Information §6" . $this->target->getName() . ":\n";
        
        if($result->num_rows > 0) {
            $data = $result->fetch_assoc();
            $rank = $data["rank_name"];
            $expirationTime = $data["expiration_time"];
            
            $content .= "§7Rank§6: " . $rank . "\n";
            
            if($expirationTime > 0) {
                $remainingTime = $expirationTime - time();
                if($remainingTime > 0) {
                    $content .= "§7Expiration: §6" . $this->formatTime($remainingTime) . "\n";
                } else {
                    $content .= "§7Expiration: §cExpired\n";
                }
            } else {
                $content .= "§7Expiration: §6Permanent\n";
            }
        } else {
            $content .= "§7Rank§6: Default\n";
            $content .= "§7Expiration: §6Permanent\n";
        }

        $this->setTitle("§l§6RANK INFO");
        $this->setContent($content);
        $this->addButton("§cClose");
    }

    private function formatTime(int $seconds): string {
        $days = floor($seconds / 86400);
        $hours = floor(($seconds % 86400) / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        
        $parts = [];
        if($days > 0) $parts[] = $days . "d";
        if($hours > 0) $parts[] = $hours . "h";
        if($minutes > 0) $parts[] = $minutes . "m";
        
        return empty($parts) ? "less than 1m" : implode(" ", $parts);
    }
}