<?php

namespace hcf\module\ranksystem\forms;

use hcf\module\ranksystem\RankManager;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\player\Player;

class PlayerRankInfoForm extends SimpleForm {

    public function __construct(private RankManager $rankManager, private Player $player) {
        parent::__construct(function(Player $player, ?int $data) {
            if($data === null) return;
        });

        $this->setTitle("§6Your Rank Information");
        $rank = $this->rankManager->getPlayerRank($player);
        $expirationTime = $this->rankManager->rankExpirations->get($player->getName(), null);
        
        $content = "§7Player: §6" . $player->getName() . "\n";
        $content .= "§7Current Rank: §6" . $rank . "\n";
        
        if ($expirationTime !== null) {
            $remainingTime = $expirationTime - time();
            if ($remainingTime > 0) {
                $content .= "§7Expiration: §6" . $this->rankManager->parseDuration($remainingTime) . "\n";
            } else {
                $content .= "§7Expiration: §cExpired\n";
            }
        } else {
            $content .= "§7Expiration: §6Permanent\n";
        }
        
        $this->setContent($content);
        $this->addButton("§cClose");
    }
}