<?php

namespace hcf\module\ranksystem\commands\subcommands;

use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\args\RawStringArgument;
use hcf\arguments\PlayersArgument;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use hcf\Loader;
use hcf\module\ranksystem\forms\RankRemoveForm;

class RemoveSubCommand extends BaseSubCommand {

    public function __construct(string $name, string $description = "")
    {
        parent::__construct($name, $description);
    }

    protected function prepare(): void {
        $this->setPermission($this->getPermission());
        $this->registerArgument(0, new PlayersArgument("player", false));
        $this->registerArgument(1, new RawStringArgument("rank", false));
    }

    public function onRun(CommandSender $sender, string $label, array $args): void {
        if (!$sender->hasPermission("ranks.commands")) {
            $sender->sendMessage(TextFormat::colorize("&cNo tienes permiso para usar este comando."));
            return;
        }

        $player = Server::getInstance()->getPlayerExact($args["player"]);

        if ($sender instanceof Player) {
            $sender->sendForm(new RankRemoveForm(Loader::getInstance()->getRankManager(), $player));
        }
    }

    public function getPermission(): ?string{
        return "ranks.commands";
    }
}
