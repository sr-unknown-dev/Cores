<?php

namespace hcf\handler\package\commands\subcommands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use hcf\abilities\items\PartnerPackages;
use hcf\arguments\PlayersArgument;
use hcf\Factory;
use hcf\Loader;
use hcf\player\Player;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class GiveSubCommand extends BaseSubCommand
{
    public function __construct(string $name, string $description = "", array $aliases = [])
    {
        parent::__construct($name, $description, $aliases);
    }

    protected function prepare(): void
    {
        $this->setPermission($this->getPermission());
        $this->registerArgument(0, new PlayersArgument("player"));
        $this->registerArgument(1, new RawStringArgument("amount"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender->hasPermission("pkg.command")) {
            $sender->sendMessage(TextFormat::RED . "You don't have permissions");
            return;
        }
        $player = $args["player"];
        $amount = $args["amount"];

        if (empty($player)) {
            $sender->sendMessage(TextFormat::RED . "/pkg give [player] [amount]");
            return;
        }
        if (empty($amount)) {
            $sender->sendMessage(TextFormat::RED . "/pkg give [player] [amount]");
            return;
        }
        PartnerPackages::addPartner($player, $amount);
    }

    public function getPermission(): ?string
    {
        return "package.give";
    }
}
?>