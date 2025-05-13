<?php

namespace hcf\module\ranksystem\commands\subcommands;

use CortexPE\Commando\BaseSubCommand;
use hcf\Loader;
use hcf\module\ranksystem\forms\RankCreateForm;
use hcf\player\Player;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class CreateSubCommand extends BaseSubCommand {

    public function __construct(string $name, string $description = "") {
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

        if ($sender instanceof Player) {
            $sender->sendForm(new RankCreateForm(Loader::getInstance()->getRankManager()));
        }
    }

    public function getPermission(): ?string {
        return "ranks.commands";
    }
}
