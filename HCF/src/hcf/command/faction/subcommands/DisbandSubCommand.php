<?php

namespace hcf\command\faction\subcommands;

use CortexPE\Commando\BaseSubCommand;
use hcf\anticheat\RemoveDataFactions;
use hcf\faction\Faction;
use hcf\Loader;
use hcf\player\Player;
use pocketmine\command\CommandSender;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

class DisbandSubCommand extends BaseSubCommand
{
    /**
     * @var $data
     */
    private $data;
    public function __construct(string $name, string $description = "", array $aliases = [])
    {
        parent::__construct($name, $description, $aliases);
        $this->data = new Config(Loader::getInstance()->getDataFolder() . "factions.json", Config::YAML, []);
    }

    protected function prepare(): void
    {
        $this->setPermission($this->getPermission());
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

        if ($faction->getRole($sender->getXuid()) !== Faction::LEADER) {
            $sender->sendMessage(TextFormat::colorize('&cYou aren\'t the leader can disband the faction'));
            return;
        }
        
        if (Loader::getInstance()->getFactionManager()->getFaction($sender->getSession()->getFaction())->getTimeRegeneration() !== null) {
            $sender->sendMessage(TextFormat::colorize("&cYou can't use this with regeneration time active!"));
            return;
        }
        $faction->disband();
        Loader::getInstance()->getFactionManager()->removeFaction($faction->getName());
        $sender->sendMessage(TextFormat::colorize('&cThe factions has disbanded'));

    }

    public function getPermission(): ?string
    {
        return "use.player.command";
    }
}