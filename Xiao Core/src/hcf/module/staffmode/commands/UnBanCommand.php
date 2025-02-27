<?php

namespace hcf\module\staffmode\commands;

use hcf\Loader;
use hcf\player\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\utils\TextFormat;

class UnBanCommand extends Command
{
    public function __construct()
    {
        parent::__construct("unban", "unbanear a un player");
        $this->setPermission("staff.cmds");
    }

    /**
     * @inheritDoc
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage(TextFormat::colorize("&cEste comando solo puede ser ejecutado por un jugador."));
            return;
        }

        if (count($args) < 1) {
            $sender->sendMessage(TextFormat::colorize("&cUso: /unban <player>"));
            return;
        }

        $t = Loader::getInstance()->getServer()->getPlayerExact($args[0]);
        if ($sender instanceof Player && $t instanceof Player)
        Loader::getInstance()->getStaffModeManager()->removeBan($sender, $t);
    }
}