<?php

namespace hcf\command\faction\subcommands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\BaseSubCommand;
use hcf\arguments\PlayersArgument;
use hcf\faction\Faction;
use hcf\Loader;
use hcf\player\Player;
use hcf\Server\Invite;
use hcf\session\Session;
use hcf\Tasks\InviteTask;
use hcf\Tasks\ThorTask;
use pocketmine\command\CommandSender;
use pocketmine\item\VanillaItems;
use pocketmine\utils\TextFormat;
use pocketmine\Server;

class InviteSubCommand extends BaseSubCommand
{
    public function __construct(string $name, string $description = "", array $aliases = [])
    {
        parent::__construct($name, $description, $aliases);
    }

    protected function prepare(): void
    {
        $this->setPermission($this->getPermission());
        $this->registerArgument(0, new PlayersArgument("player", false));
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
        
        if (!in_array($faction->getRole($sender->getXuid()), [Faction::LEADER, Faction::CO_LEADER, Faction::CAPTAIN])) {
            $sender->sendMessage(TextFormat::colorize('&cYou aren\'t the leader, co-leader or captain of the invite member'));
            return;
        }

        if (count($faction->getRoles()) === Loader::getInstance()->getConfig()->get('faction.max.members', 8)) {
            $sender->sendMessage(TextFormat::colorize('&cYour faction have max players'));
            return;
        }
        $player = $args["player"];

        if (!$player instanceof Player) {
            $sender->sendMessage(TextFormat::colorize('&cPlayer is invalid'));
            return;
        }

        if ($player->getSession()->getFaction() !== null) {
            $sender->sendMessage(TextFormat::colorize('&cThe player already has a faction'));
            return;
        }

        if (Loader::getInstance()->getFactionManager()->getFaction($sender->getSession()->getFaction())->getTimeRegeneration() !== null) {
            $sender->sendMessage(TextFormat::colorize("&cYou can't use this with regeneration time active!"));
            return;
        }
        Loader::getInstance()->getFactionManager()->createInvite($sender, $player);
        Loader::getInstance()->getScheduler()->scheduleRepeatingTask(new InviteTask($sender, $player), 20);
        $player->sendMessage(TextFormat::colorize('&a' . $sender->getName() . ' has invited you to join ' . $sender->getSession()->getFaction() . ' faction'));
        $sender->sendMessage(TextFormat::colorize('&aYou have invited ' . $player->getName() . ' to join your faction'));
    }

    public function getPermission(): ?string
    {
        return "use.player.command";
    }
}