<?php

namespace hcf\faction\command\subCommands;

use hcf\command\utils\SubCommand;
use hcf\database\Database;
use hcf\faction\Faction;
use hcf\Loader;
use hcf\player\Player;
use hcf\translation\Translation;
use hcf\translation\TranslationException;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class LeaderSubCommand extends SubCommand {

    /**
     * LeaderSubCommand constructor.
     */
    public function __construct() {
        parent::__construct("leader", "/faction leader <player>");
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     *
     * @throws TranslationException
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        if (!$sender instanceof Player) {
            return;
        }

        if (Loader::getInstance()->getFactionManager()->getFaction($sender->getName()) === null) {
            return;
        }

        if (!isset($args[1])) {
            $sender->sendMessage(TextFormat::YELLOW."/faction leader <player>");
            return;
        }

        $player = Loader::getInstance()->getServer()->getPlayerByPrefix($args[1]);

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
            Database::queryAsync("SELECT faction, username, uuid FROM players WHERE LOWER(username) = ?" , "s", [strtolower($args[1])], function(array $rows) use($sender) {
                if ($sender instanceof Player && !$sender->isOnline()) {
                    return;
                }

                if (empty($rows)) {
                    $sender->sendMessage(Translation::getMessage("invalidPlayer"));
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

                $sender->setFactionRole(Faction::OFFICER);
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
}
