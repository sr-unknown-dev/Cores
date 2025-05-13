<?php

namespace hcf\module\ranksystem\forms;

use hcf\Loader;
use hcf\module\ranksystem\RankManager;
use hcf\player\Player;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\utils\TextFormat;

class SelectRankForm extends SimpleForm
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
                $rankManager->setPlayerRank($this->targetPlayer, $data);
                $rankManager->applyPermissions($this->targetPlayer);

                $player->sendMessage(TextFormat::colorize("&8[&6Ranks&8] &aYou set {$this->targetPlayer->getName()}'s rank to {$data}"));
                $this->targetPlayer->sendMessage(TextFormat::colorize("&8[&6Ranks&8] &aYour rank has been set to {$data}"));
            }
        });

        $this->setTitle("Select Rank for " . $targetPlayer->getName());

        $rankManager = Loader::getInstance()->getRankManager();
        foreach ($rankManager->getAll() as $rankName => $rankData) {
            if ($rankName !== "default") {
                $this->addButton($rankName . "\nTap to Select", -1, "", $rankName);
            }
        }
        $this->addButton("Close", 0, "textures/ui/redX1", "close");
    }
}