<?php

namespace hcf\command\faction\subcommands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\BaseSubCommand;
use hcf\arguments\PlayersArgument;
use hcf\faction\Faction;
use hcf\Loader;
use hcf\player\Player;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use pocketmine\Server;

class LeaderSubCommand extends BaseSubCommand
{
    public function __construct(string $name, string $description = "", array $aliases = [])
    {
        parent::__construct($name, $description, $aliases);
    }

    protected function prepare(): void
    {
        $this->setPermission($this->getPermission());
        $this->registerArgument(0, new PlayersArgument("player", true));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender instanceof Player) {
            return;
        }

        if (Loader::getInstance()->getFactionManager()->getFaction($sender->getName()) === null) {
            return;
        }

        if (!isset($args["player"])) {
            $sender->sendMessage(TextFormat::YELLOW."/faction leader <player>");
            return;
        }

        $player = $args["player"];

        if ($player instanceof Player) {
            if ($player->getFaction() === null || !$player->getFaction()->isInFaction($sender)) {
                $sender->sendMessage(Translation::getMessage(
                    "notFactionMember",
                    ["name" => $player->getName()]
                ));
                return;
            }

            $sender->setFactionRole(Faction::CO_LEADER);
            $player->setFactionRole(Faction::LEADER);
            foreach ($sender->getFaction()->getOnlineMembers() as $member) {
                $member->sendMessage(Translation::getMessage("promotion", [
                    "name" => TextFormat::GREEN . $player->getName(),
                    "sender" => TextFormat::LIGHT_PURPLE . $sender->getName(),
                    "position" => TextFormat::GOLD . "Leader"
                ]));
            }
        } else {
            Database::queryAsync("SELECT faction, username, uuid FROM players WHERE LOWER(username) = ?" , "s", [strtolower($args["player"])], function(array $rows) use($sender) {
                if ($sender instanceof Player && !$sender->isOnline()) {
                    return;
                }

                if (empty($rows)) {
                    $sender->sendMessage(Transtation::getMessage("invalidPlayer"));
                    return;
                }

                $fac = $rows[0]["faction"];
                $username = $rows[0]["username"];

                if ($username === null) {
                    $sender->sendMessage(Translation::getMessage("invalidPlayer"));
                    return;
                }

                if ($fac === null) {
                    $sender->sendMessage(Translation::getMessage("noFaction", [
                        "name" => $rows[0]["username"],
                    ]));
                    return;
                }

                if ($fac !== $sender->getFaction()->getName()) {
                    $sender->sendMessage(Translation::getMessage("notFactionMember", [
                        "name" => TextFormat::RED . $username
                    ]));
                    return;
                }

                $sender->setFactionRole(Faction::LEADER);
                foreach($sender->getFaction()->getOnlineMembers() as $member) {
                    $member->sendMessage(Translation::getMessage("promotion", [
                        "name" => TextFormat::GREEN . $rows[0]["username"],
                        "sender" => TextFormat::LIGHT_PURPLE . $sender->getName(),
                        "position" => TextFormat::GOLD . "Leader"
                    ]));
                }

                Database::queryAsync("UPDATE players SET factionRole = ? WHERE uuid = ?", "is", [4, $rows[0]["uuid"]]);
            });
        }
    }

    public function getPermission(): ?string
    {
        return "use.player.command";
    }
}