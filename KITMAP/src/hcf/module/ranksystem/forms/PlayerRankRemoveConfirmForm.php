<?php

namespace hcf\module\ranksystem\forms;

use hcf\module\ranksystem\RankManager;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\player\Player;

class PlayerRankRemoveConfirmForm extends SimpleForm {

    public function __construct(private RankManager $rankManager, private Player $player, private Player $target) {
        parent::__construct(function(Player $player, ?int $data) {
            if($data === null) return;
            
            if($data === 0) {
                $this->rankManager->removePlayerRank($this->target);
                $player->sendMessage("§8[§6Ranks§8] §aSuccessfully removed rank from " . $this->target->getName());
            }
        });

        $this->setTitle("§6Remove Player Rank");
        $this->setContent("§7Are you sure you want to remove the rank from §6" . $this->target->getName() . "§7?");
        $this->addButton("§aYes\n§7Click to confirm");
        $this->addButton("§cNo\n§7Click to cancel");
    }
}