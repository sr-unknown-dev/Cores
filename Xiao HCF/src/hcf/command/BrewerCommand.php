<?php

declare(strict_types=1);

namespace hcf\command;

use hcf\player\Player;
use pocketmine\block\tile\Chest;
use pocketmine\block\VanillaBlocks;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use pocketmine\utils\TextFormat;
use pocketmine\world\World;
use pocketmine\item\PotionType;

class BrewerCommand extends Command
{

    public function __construct()
    {
        parent::__construct('brewer', '');
        $this->setAliases(['pots']);
        $this->setPermission('brewer.command');
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$sender instanceof Player)
            return;
        
        if (!$this->testPermission($sender))
            return;

        if ($sender->getSession()->getFaction() === null) {
            $sender->sendMessage(TextFormat::colorize('&cYou can\'t use this command '));
            return;
        }
        
        if ($sender->getSession()->getFaction() !== $sender->getCurrentClaim()) {
            $sender->sendMessage(TextFormat::colorize('&cYou can\'t use this command '));
            return;
        }
        
        if ($sender->getSession()->getCooldown('brewer.cooldown') !== null) {
            $sender->sendMessage(TextFormat::colorize('&cYou have cooldown to use this'));
            return;
        }
        $world = $sender->getWorld();
        $sender->getSession()->addCooldown('brewer.cooldown', '', 6 * 60 * 60, false, false);
        $position = $sender->getPosition()->floor();
        $tile1 = $this->createTileChest($position, $world);
        $tile2 = $this->createTileChest($position->add(1, 0, 0), $world);
        $tile3 = $this->createTileChest($position->add(0, 1, 0), $world);
        $tile4 = $this->createTileChest($position->add(1, 1, 0), $world);
        $tile5 = $this->createTileChest($position->add(0, 2, 0), $world);
        $tile6 = $this->createTileChest($position->add(1, 2, 0), $world);
        $splash_potion = VanillaItems::SPLASH_POTION()->setType(PotionType::STRONG_HEALING());

        for ($index = 0; $index <= 26; $index++) {
            $tile1->getInventory()->addItem($splash_potion);
            $tile2->getInventory()->addItem($splash_potion);
            $tile3->getInventory()->addItem($splash_potion);
            $tile4->getInventory()->addItem(VanillaItems::POTION()->setType(PotionType::STRONG_SWIFTNESS()));
            $tile5->getInventory()->addItem(VanillaItems::POTION()->setType(PotionType::LONG_FIRE_RESISTANCE()));
            $tile6->getInventory()->addItem(VanillaItems::POTION()->setType(PotionType::LONG_INVISIBILITY()));
        }
    }
    
    /**
     * @param Vector3 $position
     * @param World $world
     * @return Chest
     */
    private function createTileChest(Vector3 $position, World $world): Chest
    {
        if (!$world->isChunkLoaded($position->getFloorX(), $position->getFloorZ())) {
            $world->loadChunk($position->getFloorX(), $position->getFloorZ());
        }

        if ($world->getTileAt($position->getFloorX(), $position->getFloorY(), $position->getFloorZ()) != null) {
            $world->setBlock($position, VanillaBlocks::AIR());
        }

        $chest = new Chest($world, $position);
        $world->addTile($chest);
        $world->setBlock($position, VanillaBlocks::CHEST());
        return $chest;
    }
}
