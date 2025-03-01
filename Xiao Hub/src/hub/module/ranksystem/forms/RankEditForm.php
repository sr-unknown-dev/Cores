<?php

namespace hub\module\ranksystem\forms;

use hub\module\ranksystem\RankManager;
use jojoe77777\FormAPI\CustomForm;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class RankEditForm extends CustomForm {

    public function __construct(private RankManager $rankManager) {
        parent::__construct(function(Player $player, ?array $data) {
            if($data === null) return;
            
            $rankName = $data[0];
            if(!$this->rankManager->isExist($rankName)) {
                $player->sendMessage(TextFormat::colorize("&8[&6Ranks&8] &cThat rank doesn't exist!"));
                return;
            }
            
            $rankData = $this->rankManager->ranks->get($rankName);
            $rankData["format"] = $data[1];
            $rankData["chatFormat"] = $data[2];
            
            $this->rankManager->ranks->set($rankName, $rankData);
            $this->rankManager->ranks->save();
            
            $player->sendMessage(TextFormat::colorize("&8[&6Ranks&8] &aRank updated successfully!"));
        });

        $this->setTitle("Edit Rank");
        
        $ranks = [];
        foreach($this->rankManager->ranks->getAll() as $rankName => $rankData) {
            if($rankName !== "default") {
                $ranks[] = $rankName;
            }
        }
        
        $this->addDropdown("Select Rank", $ranks);
        $this->addInput("Name Format", "Enter the name format", "&l&aExample");
        $this->addInput("Chat Format", "Enter the chat format", "&8[&c{faction}&8]&r {prefix} &8[&l&aRank&8] &r&f{player}: &7{message}");
    }
}