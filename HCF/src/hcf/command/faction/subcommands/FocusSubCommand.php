<?php

namespace hcf\command\faction\subcommands;

use CortexPE\Commando\BaseSubCommand;
use hcf\arguments\FactionsArgument;
use hcf\Loader;
use hcf\player\Player;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class FocusSubCommand extends BaseSubCommand
{
    public function __construct(string $name, string $description = "", array $aliases = [])
    {
        parent::__construct($name, $description, $aliases);
    }

    protected function prepare(): void
    {
        $this->setPermission($this->getPermission());
        $this->registerArgument(0, new FactionsArgument("factionName", true));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender instanceof Player)
            return;

        if ($sender->getSession()->getFaction() === null) {
            $sender->sendMessage(TextFormat::colorize('&cYou don\'t have a faction'));
            return;
        }
        $faction = Loader::getInstance()->getFactionManager()->getFaction($sender->getSession()->getFaction());

        if (count($args) < 1) {
            $sender->sendMessage(TextFormat::colorize('&cUse /faction focus [string: name]'));
            return;
        }
        $name = $args["factionName"];

        if ($name === null) {
            $sender->sendMessage(TextFormat::colorize('&cYou must provide a valid faction name'));
            return;
        }

        $targetFaction = Loader::getInstance()->getFactionManager()->getFaction(strtolower($name));
        if ($targetFaction === null) {
            $sender->sendMessage(TextFormat::colorize('&cThe faction you\'re trying to focus does not exist'));
            return;
        }

        if (strtolower($name) === strtolower($sender->getSession()->getFaction())) {
            $sender->sendMessage(TextFormat::colorize('&cYou can\'t focus on your own faction'));
            return;
        }

        $faction->setFocus($targetFaction->getName());
        $sender->sendMessage(TextFormat::colorize('&aNow your faction is targeting the faction ' . $targetFaction->getName()));
    }

    public function getPermission(): ?string
    {
        return "use.player.command";
    }
}