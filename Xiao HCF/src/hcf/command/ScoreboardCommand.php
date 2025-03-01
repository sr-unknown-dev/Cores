<?php

declare(strict_types=1);

namespace hcf\command;

use hcf\player\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class ScoreboardCommand extends Command
{
    
    /**
     * ListCommand construct.
     */
    public function __construct()
    {
        parent::__construct('scoreboard', 'hide or show scoreboard');
        $this->setPermission("use.player.command");
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$sender instanceof Player)
            return;
        if(isset($args[0]) && $args[0] === "show"){
            $sender->setScoreboardMode();
            $sender->sendMessage(TextFormat::colorize('&a * &7you have enabled ur scoreboard'));
            return;
        }
        if(isset($args[0]) && $args[0] === "hide"){
            $sender->setScoreboardMode(false);
            $sender->sendMessage(TextFormat::colorize('&a * &7you have disabled ur scoreboard'));
            return;
        }
        $sender->sendMessage(TextFormat::colorize('&6&lScoreboard &r&8(Commands)'));
        $sender->sendMessage(TextFormat::colorize('&a * &7/scoreboard hide'));
        $sender->sendMessage(TextFormat::colorize('&a * &7/scoreboard show'));
    }
}