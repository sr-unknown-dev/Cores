<?php

namespace hcf\module\clearlag;

use hcf\Loader;
use pocketmine\entity\object\ExperienceOrb;
use pocketmine\entity\object\ItemEntity;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\SingletonTrait;
use pocketmine\utils\TextFormat;

class ClearLag
{
    use SingletonTrait;
    private const TIME = 600;

    public function clearEntities(): int
    {
        $defaultWorld = Loader::getInstance()->getServer()->getWorldManager()->getDefaultWorld();

        if ($defaultWorld === null) {
            return 0;
        }

        $clearedCount = 0;

        foreach ($defaultWorld->getEntities() as $entity) {
            if ($entity instanceof ExperienceOrb || $entity instanceof ItemEntity) {
                if (!$entity->isFlaggedForDespawn() && !$entity->isClosed()) {
                    $clearedCount++;
                    $entity->flagForDespawn();
                }
            }
        }
        return $clearedCount;
    }

    public function task(): void
    {
        $scheduler = Loader::getInstance()->getScheduler();

        $scheduler->scheduleRepeatingTask(
            new ClosureTask(function (): void {
                $clearedEntities = $this->clearEntities();

                $timeMessages = [
                    300 => "&cClearLag: cleared in 5 minutes",
                    120 => "&cClearLag: cleared in 2 minutes",
                    60 => "&cClearLag: cleared in 1 minute",
                    10 => "&cClearLag: cleared in 10 seconds",
                    9 => "&cClearLag: cleared in 9 seconds",
                    8 => "&cClearLag: cleared in 8 seconds",
                    7 => "&cClearLag: cleared in 7 seconds",
                    6 => "&cClearLag: cleared in 6 seconds",
                    5 => "&cClearLag: cleared in 5 seconds",
                    4 => "&cClearLag: cleared in 4 seconds",
                    3 => "&cClearLag: cleared in 3 seconds",
                    2 => "&cClearLag: cleared in 2 seconds",
                    1 => "&cClearLag: cleared in 1 second",
                    0 => "&cClearLag: cleared in 0 seconds"
                ];

                if (isset($timeMessages[self::TIME])) {
                    Loader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize($timeMessages[self::TIME]));
                }
                if ($clearedEntities > 0) {
                    if (self::TIME === 0) {
                        $message = TextFormat::colorize("&cClearLag: {$clearedEntities} entities cleared.");
                        Loader::getInstance()->getServer()->broadcastMessage($message);
                    }
                }
            }), self::TIME * 20);
    }
}
