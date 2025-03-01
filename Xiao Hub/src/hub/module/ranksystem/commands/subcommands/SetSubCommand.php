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

class SetSubCommand extends BaseSubCommand {

    public function __construct(string $name, string $description = "")
    {
        parent::__construct($name, $description);
    }

    protected function prepare(): void {
        $this->setPermission($this->getPermission());
        $this->registerArgument(0, new PlayersArgument("player", false));
        $this->registerArgument(1, new RawStringArgument("rank", false));
        $this->registerArgument(2, new RawStringArgument("duration", true));
    }

    public function onRun(CommandSender $sender, string $label, array $args): void {

        $player = $args["player"];
        $rank = $args["rank"];
        $durationStr = $args["duration"] ?? null;

        $rankManager = Loader::getInstance()->getRankManager();

        if ($rankManager->isExist($rank)) {
            if ($durationStr !== null) {
                $duration = $rankManager->parseDuration($durationStr);
                if ($duration === null) {
                    $sender->sendMessage(TextFormat::colorize("&cFormato de duración inválido. Usa 'Xm' para minutos."));
                    return;
                }
                $rankManager->setPlayerRank($player, $rank, $duration);
                $sender->sendMessage(TextFormat::colorize("&8[&6Ranks&8] &aHas agregado el rango: &g" . $rank . " &a a " . $player->getName() . "  &aTiempo: &g" . $durationStr));
            } else {
                $rankManager->setPlayerRank($player, $rank);
                $sender->sendMessage(TextFormat::colorize("&8[&6Ranks&8] &aHas agregado el rango: &g" . $rank . " &a a " . $player->getName() . " &aTiempo: &gPermanente"));
            }
        } else {
            $sender->sendMessage(TextFormat::colorize("&8[&6Ranks&8] &cRango no encontrado."));
        }
    }

    public function getPermission(): ?string{
        return "ranks.commands";
    }
}
