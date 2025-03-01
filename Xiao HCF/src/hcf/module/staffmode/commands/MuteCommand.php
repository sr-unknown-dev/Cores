<?php

namespace hcf\module\staffmode\commands;

use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseCommand;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use hcf\Loader;

class MuteCommand extends BaseCommand {

    public function __construct(string $name, string $description = "")
    {
        parent::__construct(Loader::getInstance(), $name, $description);
    }

    protected function prepare(): void {
        $this->setPermission($this->getPermission());
        $this->registerArgument(0, new RawStringArgument("player", true));
        $this->registerArgument(1, new RawStringArgument("time", true));
        $this->registerArgument(2, new RawStringArgument("reason", true));
    }

    public function onRun(CommandSender $sender, string $label, array $args): void {
        if (!$this->testPermission($sender)) {
            return;
        }

        if (count($args) < 3) {
            $sender->sendMessage(TextFormat::colorize("&cUse /mute <player> <time> <reason>"));
            return;
        }

        $staff = $sender;
        $target = Server::getInstance()->getPlayerExact($args["player"]);
        $reason = $args["reason"];
        $time = $args["time"];

        if ($staff instanceof Player && $target instanceof Player) {
            Loader::getInstance()->getStaffModeManager()->addMute($staff, $target, $reason, $time);
        } else {
            $sender->sendMessage(TextFormat::colorize("&cEl jugador " . $args["player"] . " no está en línea."));
        }
    }

    public function getPermission(): ?string
    {
        return "staff.cmds";
    }
}
