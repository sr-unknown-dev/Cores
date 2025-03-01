<?php

namespace hcf\addons\modules;

use hcf\Loader;
use pocketmine\block\Cobweb;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;
use pocketmine\scheduler\ClosureTask;
use pocketmine\world\Position;

class TemporalCobwebs implements Listener
{
    /**
     * @param BlockPlaceEvent $ev
     *
     * @priority HIGH
     * @ignoreCancelled false
     */
    public function onBlockPlace(BlockPlaceEvent $ev): void {
		$player = $ev->getPlayer();
		foreach($ev->getTransaction()->getBlocks() as [$x, $y, $z, $block]){
			$pos = new Position($x, $y, $z, $player->getWorld());
			if(!($b = $block) instanceof Cobweb) return;;
			if($ev->isCancelled()) $ev->uncancel();
			$old = clone $block;
			Loader::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function() use ($pos, $old): void {
				if(!($lvl = $pos->getWorld())->getBlock($pos) instanceof Cobweb) return;
				$_null = null;
				$lvl->useBreakOn($pos, $_null, null, true);
				$lvl->setBlock($pos, $old);
			}), 20 * 20);
		}

    }

}