<?php

namespace hcf\module\ranksystem;

use hcf\Loader;
use hcf\player\Player;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\player\chat\LegacyRawChatFormatter;
use pocketmine\utils\TextFormat;

class RanksListener implements Listener
{
    public function onPlayerJoin(PlayerJoinEvent $event)
    {
        $player = $event->getPlayer();
        Loader::getInstance()->getRankManager()->applyPermissions($player);
    }

    public function onPlayerChat(PlayerChatEvent $event)
    {
        $player = $event->getPlayer();
        if ($player instanceof Player) {
            $rank = Loader::getInstance()->getRankManager()->getPlayerRank($player);
            $prefix = $player->getSession()->getPrefix();
            $PName = $prefix ? Loader::getInstance()->getPrefixManager()->getPrefix($prefix)->getFormat() : "";
            $faction = $player->getSession()->getFaction();
            $FName = $faction ? Loader::getInstance()->getFactionManager()->getFaction($faction)->getName() : "";

            $data = [];
            foreach (Loader::getInstance()->getFactionManager()->getFactions() as $name => $factionObj) {
                if (!in_array($factionObj->getName(), ['Spawn', 'North Road', 'South Road', 'East Road', 'West Road', 'Nether Spawn', 'End Spawn'])) {
                    $data[$name] = $factionObj->getPoints();
                }
            }
            arsort($data);
            $topFactions = array_slice($data, 0, 3, true);

            $position = "";
            $factionPosition = array_search($FName, array_keys($topFactions)) + 1;
            if ($factionPosition >= 1 && $factionPosition <= 3) {
                $position = $factionPosition;
            }

            $chatFormat = Loader::getInstance()->getRankManager()->getChatFormat($rank);
            $format = str_replace(
                ["{top}", "{faction}", "{prefix}", "{player}", "{message}"],
                [$position, $FName, $PName, $player->getName(), $event->getMessage()],
                $chatFormat
            );

            $event->setFormatter(new LegacyRawChatFormatter(TextFormat::colorize($format)));
        }
    }
}