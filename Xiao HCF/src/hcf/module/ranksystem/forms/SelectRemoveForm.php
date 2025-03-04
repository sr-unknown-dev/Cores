<?php

namespace hcf\module\ranksystem\forms;

use hcf\Loader;
use hcf\module\ranksystem\RankManager;
use hcf\player\Player;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\TextFormat;

class SelectRemoveForm extends SimpleForm
{
    private Player $targetPlayer;

    public function __construct(Player $targetPlayer)
    {
        $this->targetPlayer = $targetPlayer;

        parent::__construct(function (Player $player, $data = null) {
            if (is_null($data) || $data === "close") {
                return;
            }

            $rankManager = Loader::getInstance()->getRankManager();
            if ($this->targetPlayer instanceof Player) {
                $rankManager->removePlayerRank($this->targetPlayer);

                $player->sendMessage(TextFormat::colorize("&8[&6Ranks&8] &aYou removed {$this->targetPlayer->getName()}'s rank {$data}"));
                $this->targetPlayer->sendMessage(TextFormat::colorize("&8[&6Ranks&8] &aYour rank {$data} has been removed"));
            }
        });

        $this->setTitle("Remove Rank from " . $targetPlayer->getName());

        $rankManager = Loader::getInstance()->getRankManager();
        $playerRank = $rankManager->getPlayerRank($targetPlayer);

        if ($playerRank !== "default") {
            $this->addButton($playerRank . "\nTap to Remove", -1, "", $playerRank);
        }
    }
}