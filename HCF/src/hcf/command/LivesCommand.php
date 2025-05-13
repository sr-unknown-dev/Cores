<?php

namespace hcf\command;

use hcf\player\Player;
use hcf\utils\inventorie\Inventories;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class LivesCommand extends Command {

    /**
     * PVPCommand constructor.
     */
    public function __construct() {
        parent::__construct("lives", "Check how much lives you've got!", "/lives");
        $this->setPermission("streamer.cmd");
    }

        /**
         * @param CommandSender $sender
         * @param string $commandLabel
         * @param array $args
         *
         * @throws TranslationException
         */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        if (!$sender instanceof Player) {
            $sender->sendMessage(TextFormat::RED."No tienes permisos para usar este comando");
            return;
        }

        if (count($args) === 0){
            $sender->sendMessage(TextFormat::RED."Porfavor proporciona el link de el video ");
            $sender->sendMessage(TextFormat::RED."Use: /lives (Link)");
            return;
        }
        $link = $args[0];

        if ($link){
            Inventories::LiveMenu($sender, $link);
        }
    }
}
