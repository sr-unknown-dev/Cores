<?php

namespace hcf\module\ranksystem\commands\subcommands;

use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\args\RawStringArgument;
use hcf\arguments\PlayersArgument;
use pocketmine\command\CommandSender;
use hcf\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use hcf\Loader;
use hcf\module\ranksystem\forms\PlayerRemoveRankForm;

class RemoveSubCommand extends BaseSubCommand {

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

        if ($sender instanceof Player){
            $sender->sendForm(new PlayerRemoveRankForm());
        }
    }

    public function getPermission(): ?string{
        return "ranks.commands";
    }
}
