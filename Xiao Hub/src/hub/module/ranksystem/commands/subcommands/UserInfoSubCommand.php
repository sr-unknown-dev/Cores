<?php

namespace hub\module\ranksystem\commands\subcommands;

use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\args\RawStringArgument;
use hub\arguments\PlayersArgument;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use hub\Loader;
use hub\module\ranksystem\forms\PlayerRankInfoForm;

class UserInfoSubCommand extends BaseSubCommand {

    protected function prepare(): void {
        $this->registerArgument(0, new PlayersArgument("player", true));
    }

    public function onRun(CommandSender $sender, string $label, array $args): void {
        if (!$sender->hasPermission("ranks.commands")) {
            $sender->sendMessage(TextFormat::colorize("&cNo tienes permiso para usar este comando."));
            return;
        }
        
        if ($sender instanceof Player) {
            $sender->sendForm(new PlayerRankInfoForm(Loader::getInstance()->getRankManager(), $args["player"]));
        }
    }
}
