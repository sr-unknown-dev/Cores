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
    private array $topFactionsCache = [];
    private int $cacheTime = 0;
    private int $cacheDuration = 60; // Duración del caché en segundos

    public function onPlayerJoin(PlayerJoinEvent $event)
    {
        $player = $event->getPlayer();
        Loader::getInstance()->getRankManager()->applyPermissions($player);
    }

    public function onPlayerChat(PlayerChatEvent $event)
    {
        $player = $event->getPlayer();
        if ($player instanceof Player) {
            $rankManager = Loader::getInstance()->getRankManager();
            $prefixManager = Loader::getInstance()->getPrefixManager();
            $factionManager = Loader::getInstance()->getFactionManager();
            $playerSession = $player->getSession();

            $rank = $rankManager->getPlayerRank($player);
            $prefix = $playerSession->getPrefix();
            $PName = $prefix ? $prefixManager->getPrefix($prefix)->getFormat() : "";
            $faction = $playerSession->getFaction();
            $FName = $faction ? $factionManager->getFaction($faction)->getName() : "";

            // Actualizar caché si es necesario
            if (time() - $this->cacheTime > $this->cacheDuration) {
                $this->updateTopFactionsCache();
            }

            $position = "";
            if (!empty($FName)) {
                $topFactionNames = array_keys($this->topFactionsCache);
                if (($factionPosition = array_search($FName, $topFactionNames)) !== false) {
                    $position = TextFormat::GREEN . "#" . ($factionPosition + 1);
                }
            }

            $chatFormat = $rankManager->getChatFormat($rank);
            $format = str_replace(
                ["{top}", "{faction}", "{prefix}", "{player}", "{message}"],
                [$position, $FName, $PName, $player->getName(), $event->getMessage()],
                $chatFormat
            );

            $event->setFormatter(new LegacyRawChatFormatter(TextFormat::colorize($format)));
        }
    }

    private function updateTopFactionsCache(): void
    {
        $factionManager = Loader::getInstance()->getFactionManager();
        $data = [];

        foreach ($factionManager->getFactions() as $name => $factionObj) {
            $factionName = $factionObj->getName();
            if (!in_array($factionName, ['Spawn', 'North Road', 'South Road', 'East Road', 'West Road', 'Nether Spawn', 'End Spawn'])) {
                $data[$factionName] = $factionObj->getPoints();
            }
        }
        arsort($data);
        $this->topFactionsCache = array_slice($data, 0, 3, true);
        $this->cacheTime = time();
    }
}
