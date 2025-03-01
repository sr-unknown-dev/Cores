<?php

namespace hub\forms;

use hub\Loader;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\world\Position;

class ModalitysForm extends SimpleForm{
    public function __construct()
    {
        parent::__construct(function (Player $player, $data = null) {
            if (is_null($data)) {
                return;
            }

            $objective = Server::getInstance()->getPlayerExact($data);

            if ($data === 0){
                $objective->sendMessage("Soon");
            }

            if ($data === 2){
                $objective->sendMessage("Soon");
            }

            if ($data === 3){
                $objective->sendMessage("Soon");
            }

        });
        $this->addButton("HCF");
        $this->addButton("KITMAP");
        $this->addButton("PRACTICE");
    }
}