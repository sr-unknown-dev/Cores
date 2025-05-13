<?php

namespace hcf\module\anticheat\commands\subcommands;

use CortexPE\Commando\BaseSubCommand;
use hcf\arguments\PlayersArgument;
use hcf\HCFLoader;
use hcf\Loader;
use hcf\player\Player;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class ExemptSubCommand extends BaseSubCommand
{

    public function __construct(string $name, string $description = "", array $aliases = [])
    {
        parent::__construct($name, $description, $aliases);
    }

    /**
     * @inheritDoc
     */
    protected function prepare(): void
    {
        $this->setPermission($this->getPermission());
        $this->registerArgument(0, new PlayersArgument("player", false));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        $player = $args["player"];
        if (empty($player)){$sender->sendMessage(TextFormat::colorize("Especifica un jugador"));}
        if($player === null){$sender->sendMessage("Player not found.");return;}

        if ($player instanceof Player)
        Loader::getInstance()->getAntiCheatManager()->toggleExemption($player);
    }

    public function getPermission(): ?string
    {
        return "anticheat.command";
    }
}