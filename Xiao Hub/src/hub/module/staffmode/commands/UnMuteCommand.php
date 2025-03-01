<?php

namespace hub\module\staffmode\commands;

use hub\Loader;
use hub\player\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class UnMuteCommand extends Command
{
    public function __construct()
    {
        parent::__construct("unmute", "unmutear a un player");
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

        if ($args[0] === null) {
            $sender->sendMessage(TextFormat::colorize("&cUso: /unmute <player>"));
            return;
        }

        $t = Loader::getInstance()->getServer()->getPlayerExact($args[0]);
        if ($sender instanceof Player && $t instanceof Player)
        Loader::getInstance()->getStaffModeManager()->removeMute($sender, $t);
    }
}