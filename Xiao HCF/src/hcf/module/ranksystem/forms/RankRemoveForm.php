<?php

namespace hcf\module\ranksystem\forms;

use hcf\module\ranksystem\RankManager;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class RankRemoveForm extends SimpleForm {

    private RankManager $rankManager;
    private Player $player;

    public function __construct(RankManager $rankManager, ?Player $player) {

        $this->rankManager = $rankManager;
        $this->player = $player;
        
        parent::__construct(function(Player $player, ?int $data) {
            if($data === null) return;
            
            $ranks = array_keys($this->rankManager->ranks->getAll());
            unset($ranks[array_search("default", $ranks)]);
            $ranks = array_values($ranks);
            
            if(isset($ranks[$data])) {
                $rankName = $ranks[$data];
                $player->sendForm(new RankRemoveConfirmForm($this->rankManager, $player, $rankName));
                $player->sendMessage(TextFormat::colorize("&8[&gRanks&8] &aHas removido el rank: &g" . $rankName . " &a de " . $player->getName()));
            }
        });

        $this->setTitle("§6Remove Rank");
        $this->setContent("§7Select a rank to remove:");
        
        foreach($this->rankManager->ranks->getAll() as $rankName => $rankData) {
            if($rankName !== "default") {
                $this->addButton("§c" . $rankName . "\n§7Click to remove");
            }
        }
    }
}