<?php

namespace org\frostcheat\hcf\modules;

use pocketmine\block\Block;
use pocketmine\block\BlockIds;
use pocketmine\level\Level;
use pocketmine\level\sound\Sound;
use pocketmine\level\particle\DestroyBlockParticle;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\utils\Config;
use pocketmine\Player;
use org\frostcheat\hcf\PocketMineLoader;
use org\frostcheat\hcf\session\Session;

use pocketmine\block\BlockFactory;
use pocketmine\world\World;

class Falltrap {
    private $skip = [];
    private $totalBlocks = 0, $processBlocks = 0;
    private $pos1, $pos2;
    private $vectors = [], $woolVectors = [];
    private $id;

    private $session;
    private $world;
    private $wool;

    public function __construct(Session $session, Vector3 $pos1, Vector3 $pos2, Level $world, Block $wool) {
        $this->session = $session;
        $this->world = $world;
        $this->wool = $wool;

        $minX = min($pos1->getFloorX(), $pos2->getFloorX());
        $minZ = min($pos1->getFloorZ(), $pos2->getFloorZ());
        $maxX = max($pos1->getFloorX(), $pos2->getFloorX());
        $maxZ = max($pos1->getFloorZ(), $pos2->getFloorZ());

        $this->pos1 = new Vector3($minX, $pos1->getFloorY(), $minZ);
        $this->pos2 = new Vector3($maxX, $pos2->getFloorY(), $maxZ);

        $config = ModuleListener::getConfig();
        $minY = $config->get("falltrap.min_y", 1);

        for ($x = $minX; $x <= $maxX; $x++) {
            for ($z = $minZ; $z <= $maxZ; $z++) {
                for ($y = $world->getHighestBlockAt($x, $z); $y >= $minY; $y--) {
                    $this->vectors[] = new Vector3($x, $y, $z);
                }
            }
        }

        for ($x = $minX; $x <= $maxX; $x++) {
            for ($y = $minY; $y <= $this->getHighestBlockAt($x, $pos1->getFloorZ(), $world, [
                BlockFactory::get(BlockIds::GRAVEL), BlockFactory::get(BlockIds::GRASS), BlockFactory::get(BlockIds::SAND), BlockFactory::get(BlockIds::RED_SANDSTONE), BlockFactory::get(BlockIds::STONE)]); $y++) {
                $this->woolVectors[] = new Vector3($x, $y, $pos1->getFloorZ());
            }
            for ($y = $minY; $y <= $this->getHighestBlockAt($x, $pos2->getFloorZ(), $world, [
                BlockFactory::get(BlockIds::GRAVEL), BlockFactory::get(BlockIds::GRASS), BlockFactory::get(BlockIds::SAND), BlockFactory::get(BlockIds::RED_SANDSTONE), BlockFactory::get(BlockIds::STONE)]); $y++) {
                $this->woolVectors[] = new Vector3($x, $y, $pos2->getFloorZ());
            }
        }

        for ($z = $minZ; $z <= $maxZ; $z++) {
            for ($y = $minY; $y <= $this->getHighestBlockAt($pos1->getFloorX(), $z, $world, [
                BlockFactory::get(BlockIds::GRAVEL), BlockFactory::get(BlockIds::GRASS), BlockFactory::get(BlockIds::SAND), BlockFactory::get(BlockIds::RED_SANDSTONE), BlockFactory::get(BlockIds::STONE)]); $y++) {
                $this->woolVectors[] = new Vector3($pos1->getFloorX(), $y, $z);
            }
            for ($y = $minY; $y <= $this->getHighestBlockAt($pos2->getFloorX(), $z, $world, [
                BlockFactory::get(BlockIds::GRAVEL), BlockFactory::get(BlockIds::GRASS), BlockFactory::get(BlockIds::SAND), BlockFactory::get(BlockIds::RED_SANDSTONE), BlockFactory::get(BlockIds::STONE)]); $y++) {
                $this->woolVectors[] = new Vector3($pos2->getFloorX(), $y, $z);
            }
        }

        $this->totalBlocks = count($this->vectors) + count($this->woolVectors);
        $this->id = uniqid();
        $this->skip[] = BlockFactory::get(BlockIds::BEDROCK);
    }

    public function getId() {
        return $this->id;
    }

    public function getPercentage() {
        return round(($this->processBlocks / (float) $this->totalBlocks) * 100.0 * 100.0) / 100.0;
    }

    public function getProgressBar() {
        $percent = (float) $this->processBlocks / $this->totalBlocks;
        $progressBars = (int) (20 * $percent);
        return "§a" . str_repeat("|", $progressBars) . "§7" . str_repeat("|", 20 - $progressBars) . " §2" . round($percent * 100.0 * 100.0) / 100.0 . "§e%";
    }

    public function getTotalBlocks() {
        return $this->totalBlocks;
    }

    public function getProcessBlocks() {
        return $this->processBlocks;
    }

    public function update() {
        if ($this->processBlocks >= $this->totalBlocks) {
            $player = $this->session->getPlayer();
            if ($player->isOnline()) {
                $player->sendMessage("§aYour falltrap has been finished");
                $packet = new PlaySoundPacket();
                $packet->soundName = "random.orb";
                $packet->x = $player->getPosition()->getFloorX();
                $packet->y = $player->getPosition()->getFloorY();
                $packet->z = $player->getPosition()->getFloorZ();
                $packet->volume = 1.0;
                $packet->pitch = 1.0;
                $player->dataPacket($packet);
            }
            $this->session->removeFalltrap($this);
            return;
        }

        $config = ModuleListener::getConfig();
        $blocksPerTick = $config->get("falltrap.blocks_per_tick_speed", 1);

        for ($c = 1; $c <= $blocksPerTick; $c++) {
            if (empty($this->woolVectors)) return;

            $skip = $this->skip;
            $woolApply = empty($this->vectors);
            if ($woolApply) {
                $skip[] = $this->wool;
            } else {
                $skip[] = BlockFactory::get(BlockIds::AIR);
            }

            $vector = $woolApply ? array_shift($this->woolVectors) : array_shift($this->vectors);
            $place = $woolApply ? $this->wool : BlockFactory::get(BlockIds::AIR);
            $this->processBlocks++;

            $block = $this->world->getBlock($vector);
            if (!in_array($block, $skip)) {
                if ($place->getId() != BlockFactory::get(BlockIds::WOOL)->getId()) {
                    $this->world->addParticle(new DestroyBlockParticle($vector, $place));
                    $this->world->addSound($vector, new Sound(Sound::BREAK_BAMBOO_WOOD));
                } else {
                    $this->world->addSound($vector, new Sound(Sound::BLOCK_BAMBOO_PLACE));
                }
                $this->world->setBlock($vector, $place);
            }
        }
    }

    private function getHighestBlockAt($x, $z, World $world, $onlyBlocks) {
        for ($y = 127; $y >= 50; --$y) {
            $b = $world->getBlock(new Vector3($x, $y, $z));
            foreach ($onlyBlocks as $block) {
                if ($b->getId() == $block->getId()) {
                    return $y;
                }
            }
        }
        return 64;
    }
}
