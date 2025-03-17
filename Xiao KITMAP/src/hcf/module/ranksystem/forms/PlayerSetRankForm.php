<?php

namespace hcf\module\ranksystem\forms;

use hcf\Loader;
use hcf\player\Player;
use jojoe77777\FormAPI\SimpleForm;

class PlayerSetRankForm extends SimpleForm
{
    public function __construct()
    {
        parent::__construct(function (Player $player, $data = null) {
            if (is_null($data)) {
                return;
            }

            if ($data === "close") {
                return;
            }

            $targetPlayer = Loader::getInstance()->getServer()->getPlayerExact($data);
            if ($targetPlayer !== null) {
                if ($targetPlayer instanceof Player)
                $player->sendForm(new SelectRankForm($targetPlayer));
            }
        });

        $this->setTitle("Player List");
        foreach (Loader::getInstance()->getServer()->getOnlinePlayers() as $player) {
            $this->addButton($player->getName() . "\nTap To Select Rank", -1, "", $player->getName());
        }
        $this->addButton("Close", 0, "textures/ui/redX1", "close");
    }
}