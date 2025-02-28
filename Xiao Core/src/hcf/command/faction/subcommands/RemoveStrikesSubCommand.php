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

class RemoveStrikesSubCommand extends BaseSubCommand
{
    public function __construct(string $name, string $description = "", array $aliases = [])
    {
        parent::__construct($name, $description, $aliases);
    }

    protected function prepare(): void
    {
        $this->setPermission($this->getPermission());
        $this->registerArgument(0, new RawStringArgument("factionName", true));
        $this->registerArgument(1, new RawStringArgument("strikes", true));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender->hasPermission('moderador.command')) {
            return;
        }
		if (count($args) < 1) {
			$sender->sendMessage(TextFormat::colorize('&cUse /faction remstrike [faction]'));
			return;
		}
		$faction = $args["factionName"];

		if ($faction === null) {
			$sender->sendMessage(TextFormat::colorize('&cFaction not exists.'));
			return;
		}
		$strikes = $args["strikes"];

		if ($strikes <= 0) {
			$sender->sendMessage(TextFormat::colorize('&cFaction no has strikes.'));
			return;
		}
		$factionInstance = Loader::getInstance()->getFactionManager()->getFaction($faction);

        $currentStrikes = $factionInstance->getStrikes();

        $newStrikes = $currentStrikes - $strikes;

        $factionInstance->setStrikes($newStrikes);
		$sender->sendMessage(TextFormat::colorize('&8[&bAdmin&8] &cYou have removed strike of ' . $faction . ' faction.'));
    }

    public function getPermission(): ?string
    {
        return "moderador.command";
    }
}