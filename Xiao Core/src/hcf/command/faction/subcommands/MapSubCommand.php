<?php

namespace hcf\command\faction\subcommands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\BaseSubCommand;
use hcf\faction\Faction;
use hcf\Loader;
use hcf\player\Player;
use pocketmine\block\Block;
use pocketmine\block\VanillaBlocks;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use pocketmine\world\Position;
use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\network\mcpe\protocol\types\BlockPosition;
use pocketmine\network\mcpe\protocol\UpdateBlockPacket;

class MapSubCommand extends BaseSubCommand
{
    public function __construct(string $name, string $description = "", array $aliases = [])
    {
        parent::__construct($name, $description, $aliases);
    }

    protected function prepare(): void
    {
        $this->setPermission($this->getPermission());
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender instanceof Player)
            return;

        if ($sender->getSession()->getFaction() === null) {
            $sender->sendMessage(TextFormat::colorize('&cYou don\'t have a faction'));
            return;
        }

        $faction = $sender->getSession()->getFaction();
        $factionpura = Loader::getInstance()->getFactionManager()->getFaction($faction);
        $claim = Loader::getInstance()->getClaimManager()->getClaim($factionpura->getName());
        if ($claim === null){
            $sender->sendMessage(TextFormat::colorize('&cYou dont have a claim'));
            return;
        }
        $x = $claim->getMaxX();
        $z = $claim->getMaxZ();
        if ($x === null || $z === null){
            $sender->sendMessage(TextFormat::colorize('&cYou dont have a claim'));
            return;
        }
        for ($y = $sender->getPosition()->getFloorY(); $y <= 127; $y++) {
            $sender->getNetworkSession()->sendDataPacket($this->sendFakeBlock(new Position($x, $y, $z, $sender->getWorld()), $y % 3 === 0 ? VanillaBlocks::GOLD() : VanillaBlocks::GLASS()));
        }
        $x = $claim->getMinX();
        $z = $claim->getMinZ();
        for ($y = $sender->getPosition()->getFloorY(); $y <= 127; $y++) {
            $sender->getNetworkSession()->sendDataPacket($this->sendFakeBlock(new Position($x, $y, $z, $sender->getWorld()), $y % 3 === 0 ? VanillaBlocks::GOLD() : VanillaBlocks::GLASS()));
        }
        $x = $claim->getMinX();
        $z = $claim->getMaxZ();
        for ($y = $sender->getPosition()->getFloorY(); $y <= 127; $y++) {
            $sender->getNetworkSession()->sendDataPacket($this->sendFakeBlock(new Position($x, $y, $z, $sender->getWorld()), $y % 3 === 0 ? VanillaBlocks::GOLD() : VanillaBlocks::GLASS()));
        }
        $x = $claim->getMaxX();
        $z = $claim->getMinZ();
        for ($y = $sender->getPosition()->getFloorY(); $y <= 127; $y++) {
            $sender->getNetworkSession()->sendDataPacket($this->sendFakeBlock(new Position($x, $y, $z, $sender->getWorld()), $y % 3 === 0 ? VanillaBlocks::GOLD() : VanillaBlocks::GLASS()));
        }
    }
    /**
     * @param Block $block
     * @return UpdateBlockPacket
     */
    private function sendFakeBlock(Position $position, Block $block): UpdateBlockPacket
    {
        $pos = BlockPosition::fromVector3($position->asVector3());
        $id = TypeConverter::getInstance()->getBlockTranslator()->internalIdToNetworkId($block->getStateId());
        $pk = UpdateBlockPacket::create($pos, $id, UpdateBlockPacket::FLAG_NETWORK, UpdateBlockPacket::DATA_LAYER_NORMAL);
        return $pk;
    }

    public function getPermission(): ?string
    {
        return "use.player.command";
    }
}