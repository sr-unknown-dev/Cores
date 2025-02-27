<?php

namespace hcf\command\faction\subcommands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\BaseSubCommand;
use hcf\arguments\FactionsArgument;
use hcf\faction\Faction;
use hcf\Loader;
use hcf\player\Player;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class AddPointsSubCommand extends BaseSubCommand
{
    public function __construct(string $name, string $description = "", array $aliases = [])
    {
        parent::__construct($name, $description, $aliases);
    }

    protected function prepare(): void
    {
        $this->setPermission($this->getPermission());
        $this->registerArgument(0, new RawStringArgument("factionName", false));
        $this->registerArgument(1, new RawStringArgument("Dtr", false));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender->hasPermission('moderador.command')) {
            return;
        }
		if (count($args) < 2) {
			$sender->sendMessage(TextFormat::colorize('&cUse /faction addpoint [faction] [points]'));
			return;
		}
		$faction = $args["factionName"];

		if ($faction === null) {
			$sender->sendMessage(TextFormat::colorize('&cFaction not exists.'));
			return;
		}

		if (!is_numeric($args["Dtr"])) {
			$sender->sendMessage(TextFormat::colorize('&cInvalid number.'));
			return;
		}
		$points = (int) $args["Dtr"];

		if ($points <= 0) {
			$sender->sendMessage(TextFormat::colorize('&cNumber is less than or equal to 0'));
			return;
		}
		$factionInstance = Loader::getInstance()->getFactionManager()->getFaction($faction);

        $currentStrikes = $factionInstance->getPoints();

        $newStrikes = $currentStrikes + $points;

        $factionInstance->setPoints($newStrikes);
		$sender->sendMessage(TextFormat::colorize('&8[&bAdmin&8] &aYou have updated points of ' . $faction . ' faction.'));
    }

    public function getPermission(): ?string
    {
        return "moderador.command";
    }
}