<?php
declare(strict_types=1);

namespace hcf\pm5;

use pocketmine\event\player\PlayerItemUseEvent;
use hcf\player\Player as HCFPlayer;
use pocketmine\utils\TextFormat;
use pocketmine\item\VanillaItems;
use pocketmine\event\Listener;
use pocketmine\event\entity\ProjectileHitEvent;
use pocketmine\entity\projectile\SplashPotion;
use pocketmine\event\entity\ProjectileHitBlockEvent;
use pocketmine\item\PotionType;
use pocketmine\block\VanillaBlocks;

class pm5Listener implements Listener{
	public function ItemUse(PlayerItemUseEvent $event) : void{
		$player = $event->getPlayer();
		$item = $event->getItem();

		if($item->equals(VanillaItems::ENDER_PEARL(), false, false)&&$player instanceof HCFPlayer){
			$session = $player->getSession();

			if($player->getCurrentClaim() === '§5Citadel§c'){
				$player->sendMessage(TextFormat::colorize('&cYou can\'t use this in &5Citadel &cclaim.'));
				$event->cancel();
				return;
			}

			if($session->getCooldown('enderpearl') !== null){
				$player->sendMessage(TextFormat::colorize('&cYou have cooldown enderpearl'));
				$event->cancel();
				return;
			}
			$session->addCooldown('enderpearl', '&l&5Enderpearl&r&7: &r&c', 15);
		}
	}

	/**
	 * @see SplashPotionEntity
	 *
	 * @param ProjectileHitEvent $event
	 * @return void
	 */
	public function ProjectileHit(ProjectileHitEvent $event) : void{
		$entity = $event->getEntity();
		if($entity instanceof SplashPotion){
			if(count($entity->getPotionEffects()) !== 0){
				if ($event instanceof ProjectileHitBlockEvent and $entity->getPotionType()->equals(PotionType::WATER())) {
					$blockIn = $event->getBlockHit()->getSide($event->getRayTraceResult()->getHitFace());

					if ($blockIn->hasSameTypeId(VanillaBlocks::FIRE()))
						$entity->getWorld()->setBlock($blockIn->getPosition(), VanillaBlocks::AIR());

					foreach ($blockIn->getHorizontalSides() as $horizontalSide) {
						if ($horizontalSide->hasSameTypeId(VanillaBlocks::FIRE()))
							$entity->getWorld()->setBlock($horizontalSide->getPosition(), VanillaBlocks::AIR());
					}
				}
			}

		}
	}
}
