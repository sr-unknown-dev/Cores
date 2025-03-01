<?php

namespace hub\module\ranksystem\forms;

use hub\module\ranksystem\RankManager;
use jojoe77777\FormAPI\CustomForm;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class RankCreateForm extends CustomForm {

    public function __construct(private RankManager $rankManager) {
        parent::__construct(function(Player $player, ?array $data) {
            if($data === null) return;
            
            $rankName = trim($data[0]);
            $nameFormat = $data[1];
            $chatFormat = $data[2];
            
            if(empty($rankName) || empty($nameFormat)) {
                $player->sendMessage(TextFormat::colorize("&8[&6Ranks&8] &cPlease fill all required fields!"));
                return;
            }
            
            if($this->rankManager->isExist($rankName)) {
                $player->sendMessage(TextFormat::colorize("&8[&6Ranks&8] &cThis rank already exists!"));
                return;
            }

            $previewNameFormat = str_replace("&", "§", $nameFormat);
            $previewChatFormat = str_replace([
                "&",
                "{player}",
                "{message}",
                "{prefix}",
                "{faction}"
            ], [
                "§",
                $player->getName(),
                "Hola como estas",
                $previewNameFormat,
                "Faction"
            ], $chatFormat);

            $this->rankManager->createRank($player, $rankName, $nameFormat);
            
            $player->sendMessage(TextFormat::colorize("&8[&6Ranks&8] &aRank created successfully!"));
            $player->sendMessage(TextFormat::colorize("&8[&6Ranks&8] &7Name Format Preview: " . $previewNameFormat));
            $player->sendMessage(TextFormat::colorize("&8[&6Ranks&8] &7Chat Format Preview: " . $previewChatFormat));
        });

        $this->setTitle("§6Create New Rank");
        $this->addInput("§7Rank Name", "Enter the rank name", "");
        $this->addInput("§7Name Format", "Example: &l&aAdmin", "&l&aAdmin");
        $this->addInput("§7Chat Format", "Enter chat format", "&8[&c{faction}&8]&r {prefix} &8[{rank}&8] &r&f{player}: &7{message}");
        $this->addLabel("§7Available format codes:\n" .
            "§7- {player} : Player name\n" .
            "§7- {prefix} : Rank format\n" .
            "§7- {faction} : Player faction\n" .
            "§7- {message} : Player message\n" .
            "§7Color codes use & symbol (e.g., &a, &l, &r)");
    }
}