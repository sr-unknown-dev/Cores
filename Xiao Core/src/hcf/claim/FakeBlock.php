<?php

namespace hcf\claim;

use hcf\player\Player;
use pocketmine\block\Block;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\network\mcpe\protocol\types\BlockPosition;
use pocketmine\network\mcpe\protocol\UpdateBlockPacket;
use pocketmine\utils\SingletonTrait;

class FakeBlock
{
    use SingletonTrait;

    public static function showBlock(Player $player, Block $block, Vector3 $pos, bool $immediate = false) {
		$player->getNetworkSession()->sendDataPacket(self::showBlockPacket($block, $pos), $immediate);
	}

	public static function showBlockPacket(Block $block, Vector3 $pos) {
		$newPos= BlockPosition::fromVector3($pos);
		$block = TypeConverter::getInstance()->getBlockTranslator()->internalIdToNetworkId($block->getStateId());
		return UpdateBlockPacket::create($newPos, $block, UpdateBlockPacket::FLAG_NETWORK, UpdateBlockPacket::DATA_LAYER_NORMAL);
	}
}