<?php

namespace hub\module\clearlag;

use hub\Loader;
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

                if (self::TIME === 300){Loader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&cClearLag: cleared in 5 minutes"));}
                if (self::TIME === 120){Loader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&cClearLag: cleared in 2 minutes"));}
                if (self::TIME === 60){Loader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&cClearLag: cleared in 1 minutes"));}
                if (self::TIME === 10){Loader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&cClearLag: cleared in 10 seconds"));}
                if (self::TIME === 9){Loader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&cClearLag: cleared in 9 seconds"));}
                if (self::TIME === 8){Loader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&cClearLag: cleared in 8 seconds"));}
                if (self::TIME === 7){Loader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&cClearLag: cleared in 7 seconds"));}
                if (self::TIME === 6){Loader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&cClearLag: cleared in 6 seconds"));}
                if (self::TIME === 5){Loader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&cClearLag: cleared in 5 seconds"));}
                if (self::TIME === 4){Loader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&cClearLag: cleared in 4 seconds"));}
                if (self::TIME === 3){Loader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&cClearLag: cleared in 3 seconds"));}
                if (self::TIME === 2){Loader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&cClearLag: cleared in 2 seconds"));}
                if (self::TIME === 1){Loader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&cClearLag: cleared in 1 seconds"));}
                if (self::TIME === 0){Loader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&cClearLag: cleared in 0 seconds"));}
                if ($clearedEntities > 0) {
                    if (self::TIME === 0) {
                        $message = TextFormat::colorize("&cClearLag: {$clearedEntities} entities cleared.");
                        Loader::getInstance()->getServer()->broadcastMessage($message);
                    }
                }
            }), self::TIME * 20);
    }
}
