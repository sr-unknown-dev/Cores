<?php

namespace hcf\command\fix\subcommand;

use CortexPE\Commando\BaseSubCommand;
use hcf\Loader;
use hcf\player\Player;
use hcf\Server\ServerA;
use hcf\Tasks\AutoFixTask;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat as TF;

class AutoSubCommand extends BaseSubCommand {

    public function __construct(string $name, string $description = "", array $aliases = []){parent::__construct($name, $description, $aliases);}

    public function prepare(): void {
        $this->setPermission($this->getPermission());
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
        if (!$sender instanceof Player) {
            $sender->sendMessage(TF::RED . "Este comando solo puede ser usado por jugadores.");
            return;
        }

        $playerName = $sender->getName();
        $isAutoFixEnabled = isset(ServerA::$fix[$playerName]);

        ServerA::$fix[$playerName] = !$isAutoFixEnabled;
        
        $message = $isAutoFixEnabled ? 
            TF::RED . "Has desactivado el AutoFix." : 
            TF::GREEN . "Has activado el AutoFix.";
        
        $sender->sendMessage($message);
        
        if (!$isAutoFixEnabled) {
            Loader::getInstance()->getScheduler()->scheduleRepeatingTask(new AutoFixTask($sender), 2400);
        }
    }

    public function getPermission(): string {
        return "autofix.player.command";
    }
}
