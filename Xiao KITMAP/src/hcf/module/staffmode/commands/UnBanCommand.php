<?php

namespace hcf\module\staffmode\commands;

use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseCommand;
use hcf\arguments\PlayersArgument;
use pocketmine\command\CommandSender;
use hcf\player\Player;
use pocketmine\utils\TextFormat;
use hcf\Loader;

class UnBanCommand extends BaseCommand {

    public function __construct(string $name, string $description = "")
    {
        parent::__construct(Loader::getInstance(), $name, $description);
    }

    protected function prepare(): void {
        $this->setPermission($this->getPermission());
        $this->registerArgument(0, new RawStringArgument("player", true));
    }

    public function onRun(CommandSender $sender, string $label, array $args): void {
        if (!$sender instanceof Player) {
            $sender->sendMessage(TextFormat::colorize("&cEste comando solo puede ser ejecutado por un jugador."));
            return;
        }

        if (count($args) < 1) {
            $sender->sendMessage(TextFormat::colorize("&cUso: /unban <player>"));
            return;
        }

        $target = $args["player"];

        if ($sender instanceof Player) {
            Loader::getInstance()->getStaffModeManager()->removeBan($sender, $target);
        }
    }

    public function getPermission(): ?string{
        return "staff.cmds";
    }
}
