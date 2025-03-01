<?php

namespace hub\module\ranksystem\forms;

use hub\module\ranksystem\RankManager;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\player\Player;

class RankListInfoForm extends SimpleForm {

    public function __construct(private RankManager $rankManager) {
        parent::__construct(function(Player $player, ?int $data) {
            if($data === null) return;
        });

        $this->setTitle("§6Available Ranks");
        $content = "";
        $ranks = $this->rankManager->ranks->getAll();
        
        foreach($ranks as $rankName => $rankData) {
            if($rankName !== "default") {
                $format = $rankData["format"] ?? "N/A";
                $content .= "§7Rank: §6{$rankName}\n";
                $content .= "§7Format: " . str_replace("&", "§", $format) . "\n\n";
            }
        }
        
        $this->setContent($content);
        $this->addButton("§cClose");
    }
}