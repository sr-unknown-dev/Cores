<?php

namespace daily\Command;

use daily\Main;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use daily\Utils\Inventorys;
use daily\Cooldowns\Cooldown;

class DailyCommand extends Command {

    private $plugin;
    private $cooldown;

    public function __construct($plugin) {
        parent::__construct("daily", "comando para menu de dailys");
        $this->setPermission("use.player.command");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args):void{
        if(!$sender instanceof Player)
            return;
        
            $playerName = $sender->getName();

            if ($sender instanceof Player) {
                Inventorys::DailyMenu($sender);
            }
    }
}