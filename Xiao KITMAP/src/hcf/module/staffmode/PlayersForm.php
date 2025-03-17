<?php

namespace hcf\module\staffmode;

use hcf\Loader;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\world\Position;

class PlayersForm extends SimpleForm
{
    public function __construct()
    {
        parent::__construct(function (Player $player, $data = null) {
            if (is_null($data)) {
                return;
            }

            $objective = Server::getInstance()->getPlayerExact($data);

            if (!$objective instanceof Player) {
                $player->sendMessage("This Player is Not Online");
                return;
            }

            $x = $objective->getPosition()->getX();
            $y = $objective->getPosition()->getY();
            $z = $objective->getPosition()->getZ();
            $world = $objective->getPosition()->getWorld();

            $player->teleport(new Position($x, $y, $z, $world));
            $player->sendMessage("Successfully Teleported To §e" . $objective->getName());

        });
        $this->setTitle("Player List");
        foreach (Loader::getInstance()->getServer()->getOnlinePlayers() as $player) {
            $this->addButton($player->getName() . "\nTap To Teleport", 0, "textures/ui/icon_steve", $player->getName());
        }
        $this->addButton("Close", 0, "textures/ui/redX1", "close");
    }
}