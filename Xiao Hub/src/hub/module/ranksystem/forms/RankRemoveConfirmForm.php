<?php

namespace hub\module\ranksystem\forms;

use hub\module\ranksystem\RankManager;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\player\Player;

class RankRemoveConfirmForm extends SimpleForm {

    public function __construct(private RankManager $rankManager, private Player $player, private string $rankName) {
        parent::__construct(function(Player $player, ?int $data) {
            if($data === null) return;
            
            if($data === 0) {
                $this->rankManager->deleteRank($player, $this->rankName);
            }
        });

        $this->setTitle("§6Confirm Removal");
        $this->setContent("§7Are you sure you want to remove the rank §6" . $this->rankName . "§7?");
        $this->addButton("§aYes\n§7Click to confirm");
        $this->addButton("§cNo\n§7Click to cancel");
    }
}