<?php

namespace hcf\module\clearlag;

use hcf\Loader;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class ClearLagCommand extends Command
{
    public function __construct()
    {
        parent::__construct("clearlag", "Borra todas las entidades de el mundo");
        $this->setPermission("clearlag.command");
    }

    /**
     * @inheritDoc
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (!$sender->hasPermission("clearlag.command")) return;
        $clearedEntities = ClearLag::getInstance()->clearEntities();
        $message = TextFormat::colorize("&cClearLag: {$clearedEntities} entities cleared.");
        Loader::getInstance()->getServer()->broadcastMessage($message);
    }
}