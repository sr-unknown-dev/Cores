<?php

namespace hub\module\ranksystem\forms;

use hub\module\ranksystem\RankManager;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\player\Player;

class RankInfoForm extends SimpleForm {

    public function __construct(private RankManager $rankManager, private Player $player) {
        parent::__construct(function(Player $player, ?int $data) {
            if($data === null) return;
            
            if($data === 0) {
                $player->sendForm(new RankListInfoForm($this->rankManager));
            } else if($data === 1) {
                $player->sendForm(new PlayerRankInfoForm($this->rankManager, $player));
            }
        });

        $this->setTitle("§6Rank Information");
        $this->setContent("§7Select an option to view information:");
        $this->addButton("§aAll Ranks\n§7Click to view");
        $this->addButton("§aYour Ranks\n§7Click to view");
    }
}