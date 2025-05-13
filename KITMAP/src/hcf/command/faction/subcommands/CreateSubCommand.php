<?php

namespace hcf\command\faction\subcommands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use hcf\anticheat\AddDataFactions;
use hcf\faction\Faction;
use hcf\Loader;
use hcf\player\Player;
use pocketmine\command\CommandSender;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

class CreateSubCommand extends BaseSubCommand
{

    private $data;
    public function __construct(string $name, string $description = "", array $aliases = [])
    {
        parent::__construct($name, $description, $aliases);
        $this->data = new Config(Loader::getInstance()->getDataFolder(). 'factions.yml', Config::YAML);
    }

    protected function prepare(): void
    {
        $this->setPermission($this->getPermission());
        $this->registerArgument(0, new RawStringArgument("Name"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender instanceof Player)
            return;

        if ($sender->getSession()->getFaction() !== null) {
            $sender->sendMessage(TextFormat::colorize('&cYou already have a faction'));
            return;
        }

        if (count($args) < 1) {
            $sender->sendMessage(TextFormat::colorize('&cUse /f create [string: name]'));
            return;
        }
        $factionName = $args["Name"];

        if (is_numeric($factionName)) {
            return;
        }

        if (Loader::getInstance()->getFactionManager()->getFaction($factionName) !== null || Loader::getInstance()->getClaimManager()->getClaim($factionName) !== null) {
            $sender->sendMessage(TextFormat::colorize('&cA faction or a claim already exists with this name'));
            return;
        }

        if (strlen($factionName) < 4) {
            $sender->sendMessage(TextFormat::colorize('&cYour faction must have more than 5 characters to create it!'));
            return;
        }

        if (strlen($factionName) > 10) {
            $sender->sendMessage(TextFormat::colorize('&cYour faction name cannot contain more than 10 characters'));
            return;
        }
        $checkName = explode(' ', $factionName);

        if (count($checkName) > 1) {
            $sender->sendMessage(TextFormat::colorize('&cYour faction name cannot contain spaces'));
            return;
        }

        if (in_array($factionName, ['Spawn', 'Nether-Spawn', 'End-Spawn'])) {
            $sender->sendMessage(TextFormat::colorize('&cInvalid name'));
            return;
        }

        if (Loader::getInstance()->getTimerManager()->getEotw()->isActive()){
            $sender->sendMessage(TextFormat::RED."You can't put create faction in eotw");
            return;
        }
        
        Loader::getInstance()->getFactionManager()->createFaction($factionName, [
            'roles' => [
                $sender->getXuid() => Faction::LEADER
            ],
            'dtr' => 1.1,
            'balance' => 0,
            'points' => 0,
            'kothCaptures' => 0,
            'timeRegeneration' => 0,
            'home' => null,
            'claim' => null
        ]);
        $sender->getSession()->setFaction($factionName);
        $sender->setScoreTag(TextFormat::colorize('&6[&c'.$factionName.' &c1.0■&6]'));
        $sender->sendMessage(TextFormat::colorize('&aYou have created the faction'));
        $sender->getServer()->broadcastMessage(TextFormat::colorize('§gTeam §c' . $factionName . ' §7has been &acreated &eby §b' . $sender->getName()));
    }

    public function getPermission(): ?string
    {
        return "use.player.command";
    }
}