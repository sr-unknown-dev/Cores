<?php

namespace hcf\module\staffmode\commands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseCommand;
use hcf\arguments\PlayersArgument;
use hcf\Loader;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class BanCommand extends BaseCommand {

    public function __construct(string $name, string $description = "")
    {
        parent::__construct(Loader::getInstance(), $name, $description);
    }

    protected function prepare(): void {
        $this->setPermission($this->getPermission());
        $this->registerArgument(0, new PlayersArgument("player", true));
        $this->registerArgument(1, new RawStringArgument("time", true));
        $this->registerArgument(2, new RawStringArgument("reason", true)); // Cambiado a false para permitir espacios
    }

    public function onRun(CommandSender $sender, string $label, array $args): void {
        if (!$sender instanceof Player) {
            $sender->sendMessage(TextFormat::colorize("&cEste comando solo puede ser ejecutado por un jugador."));
            return;
        }

        if (count($args) < 3) {
            $sender->sendMessage(TextFormat::colorize("&cUso: /ban <player> <time> <reason>"));
            return;
        }
    
        $target = $args["player"];
        $time = $args["time"];
        $reason = implode(" ", array_slice($args, 2)); // Esto combinará todas las palabras después del tiempo
    
        if ($target instanceof Player) {
            Loader::getInstance()->getStaffModeManager()->addBan($sender, $target, $reason, $time);
        } else {
            $sender->sendMessage(TextFormat::colorize("&cEl jugador no está en línea."));
        }
    }

    public function getPermission(): ?string{
        return "staff.cmds";
    }
}
