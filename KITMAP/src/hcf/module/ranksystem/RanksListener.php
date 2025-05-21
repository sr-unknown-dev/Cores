<?php

namespace hcf\module\ranksystem;

use hcf\Loader;
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

        $rankManager = Loader::getInstance()->getRankManager();
        $prefixManager = Loader::getInstance()->getPrefixManager();
        $factionManager = Loader::getInstance()->getFactionManager();
        $playerSession = $player->getSession();

        $rank = $rankManager->getPlayerRank($player);
        $tagName = $prefixManager->getTagName($player->getName());
        $PName = $tagName !== null ? $prefixManager->getTagFormat($tagName) : "";

        $factionName = "";
        $faction = $playerSession->getFaction();
        if ($faction !== null) {
            $factionObj = $factionManager->getFaction($faction);
            $factionName = $factionObj !== null ? $factionObj->getName() : "";
        }

        if (time() - $this->cacheTime > $this->cacheDuration) {
            $this->updateTopFactionsCache();
        }

        $position = "";
        if (!empty($factionName)) {
            $topFactionNames = array_keys($this->topFactionsCache);
            if (($factionPosition = array_search($factionName, $topFactionNames)) !== false) {
                $position = TextFormat::GREEN . "#" . ($factionPosition + 1);
            }
        }

        $chatFormat = $rankManager->getChatFormat($rank);
        $format = str_replace(
            ["{top}", "{faction}", "{prefix}", "{player}", "{message}"],
            [$position, $factionName, $PName, $player->getName(), $event->getMessage()],
            $chatFormat
        );

        $event->setFormatter(new LegacyRawChatFormatter(TextFormat::colorize($format)));
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
