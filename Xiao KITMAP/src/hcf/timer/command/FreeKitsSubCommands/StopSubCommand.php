<?php

namespace hcf\timer\command\FreeKitsSubCommands;

use CortexPE\Commando\BaseSubCommand;
use hcf\command\CommandManager;
use hcf\player\Player;
use hcf\timer\types\TimerFreeKits;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class StopSubCommand extends BaseSubCommand {
    
    public function __construct() {
        parent::__construct("stop", "Stop the timer", []);
    }

    protected function prepare(): void {
        $this->setPermission("moderador.command");
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
        if (!$sender instanceof Player) {
            return;
        }

        if (!$sender->hasPermission("moderador.command")) {
            $sender->sendMessage(TextFormat::colorize('&cNo tienes permisos para usar este comando'));
            return;
        }
        if(!TimerFreeKits::isEnable()){
            $sender->sendMessage(TextFormat::colorize('&cEl evento nunca se inició, ¡no puedes hacer esto!'));
            return;
        }
        TimerFreeKits::stop();
    }
}