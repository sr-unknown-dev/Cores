<?php

namespace hcf\command;

use hcf\Loader;
use hcf\player\Player;
use hcf\utils\time\Timer;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class FreeRankCommand extends Command 

{

    public function __construct(){
        parent::__construct('freerank', 'Command for freerank');
        $this->setPermission("use.player.command");

    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)

    {
        if (!$sender instanceof Player) {
            return;
        }
        
        if ($sender->getSession() == null) {
            return;
        }
        
        if (!$sender->getSession()->getCooldown("freerank.cooldown") == null) {
            $sender->sendMessage(TextFormat::colorize("&cYou already used this command"));
            return;
        }

        $rankManager = Loader::getInstance()->getRankManager();

        $rankManager->setPlayerRank($sender, "Leviathan", 259200 );
        $sender->getSession()->addCooldown('freerank.cooldown', '', 604800, false, false);
        $sender->sendMessage(TextFormat::colorize("&aHas recivido el rank &1Leviathan &apor &a3 Dias"));

    }

}