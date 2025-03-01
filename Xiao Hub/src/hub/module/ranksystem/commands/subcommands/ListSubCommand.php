<?php

namespace hub\module\ranksystem\commands\subcommands;

use CortexPE\Commando\BaseSubCommand;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use hub\Loader;

class ListSubCommand extends BaseSubCommand {

    public function __construct(string $name, string $description = "")
    {
        parent::__construct($name, $description);
    }

    protected function prepare(): void {
        $this->setPermission($this->getPermission());
        
    }

    public function onRun(CommandSender $sender, string $label, array $args): void {
        if (!$sender->hasPermission("ranks.commands")) {
            $sender->sendMessage(TextFormat::colorize("&cNo tienes permiso para usar este comando."));
            return;
        }

        Loader::getInstance()->getRankManager()->RankList($sender);
    }

    public function getPermission(): ?string{
        return "ranks.commands";
    }
}
