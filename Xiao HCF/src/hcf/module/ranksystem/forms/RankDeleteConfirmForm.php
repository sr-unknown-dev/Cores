<?php

namespace hcf\module\ranksystem\forms;

use hcf\module\ranksystem\RankManager;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\player\Player;

class RankDeleteConfirmForm extends SimpleForm {

    public function __construct(private RankManager $rankManager, private Player $player, private string $rankName) {
        parent::__construct(function(Player $player, ?int $data) {
            if($data === null) return;
            
            if($data === 0) {
                $this->rankManager->deleteRank($player, $this->rankName);
            }
        });

        $this->setTitle("§6Delete Rank");
        $this->setContent("§c⚠ Warning: This action cannot be undone!\n\n§7Are you sure you want to completely delete the rank §6" . $this->rankName . "§7?");
        $this->addButton("§aYes\n§7Click to delete");
        $this->addButton("§cNo\n§7Click to cancel");
    }
}