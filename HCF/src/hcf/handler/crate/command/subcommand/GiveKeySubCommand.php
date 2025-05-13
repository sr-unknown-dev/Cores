<?php

namespace hcf\handler\crate\command\subcommand;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\BaseSubCommand;
use hcf\arguments\CratesArgument;
use hcf\arguments\PlayersArgument;
use hcf\faction\Faction;
use hcf\Loader;
use hcf\player\Player;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class GiveKeySubCommand extends BaseSubCommand
{
    public function __construct(string $name, string $description = "", array $aliases = [])
    {
        parent::__construct($name, $description, $aliases);
    }

    protected function prepare(): void
    {
        $this->setPermission($this->getPermission());
        $this->registerArgument(0, new RawStringArgument("crateName"));
        $this->registerArgument(1, new PlayersArgument("player"));
        $this->registerArgument(2, new RawStringArgument("amount"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender instanceof Player)
            return;

        if (count($args) < 3) {
            $sender->sendMessage(TextFormat::colorize('&cUse /crate giveKey [string: crateName] [string: playerName] [int: amount]'));
            return;
        }
        $crateName = $args["crateName"];
        $player = $args["player"];
        $amount = $args["amount"];

        $crate = Loader::getInstance()->getHandlerManager()->getCrateManager()->getCrate($crateName);

        if ($crate === null) {
            $sender->sendMessage(TextFormat::colorize('&cThis crate does not exist'));
            return;
        }

        if ($player === 'all') {
            if (!is_numeric($amount)) {
                $sender->sendMessage(TextFormat::colorize('&cAmount invalid'));
                return;
            }

            foreach ($sender->getServer()->getOnlinePlayers() as $player) {
                $crate->giveKey($player, (int) $amount);
                $player->sendMessage(TextFormat::colorize('&aYou have received ' . $amount . 'x of ' . $crate->getKeyFormat()));
            }
        } else {

            if (!is_numeric($amount)) {
                $sender->sendMessage(TextFormat::colorize('&cAmount invalid'));
                return;
            }
            $crate->giveKey($player, (int) $amount);
            $player->sendMessage(TextFormat::colorize('&aYou have received ' . $amount . 'x of ' . $crate->getKeyFormat()));
            $sender->sendMessage(TextFormat::colorize('&aYou have given ' . $player->getName() . ' ' . $amount . 'x amount of ' . $crate->getKeyFormat()));
        }
    }

    public function getPermission(): ?string
    {
        return "crate.command.givekey";
    }
}