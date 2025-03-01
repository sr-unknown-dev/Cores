<?php

declare(strict_types=1);

namespace hcf\item;

use hcf\entity\default\EnderpearlEntity;
use JetBrains\PhpStorm\Pure;
use hcf\Loader;
use hcf\player\Player as HCFPlayer;
use pocketmine\entity\Location;
use pocketmine\entity\projectile\Throwable;
use pocketmine\item\EnderPearl as PMEnderPearl;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemIds;
use pocketmine\item\ItemTypeIds;
use pocketmine\item\ItemUseResult;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\TextFormat;

class EnderpearlItem extends PMEnderPearl
{
    
    /**
     * EnderpearlItem construct.
     */
    public function __construct()
    {
        parent::__construct(new ItemIdentifier(ItemTypeIds::ENDER_PEARL, 0), 'Ender Pearl');
    }

    /**
     * @param Location $location
     * @param Player $thrower
     * @return Throwable
     */
    protected function createEntity(Location $location, Player $thrower): Throwable
    {
        return new EnderpearlEntity($location, $thrower);
    }
    
    /**
     * @return float
     */
    public function getThrowForce(): float
    {
        return 2.1;
    }
    
    /**
     * @param Player $player
     * @param Vector3 $directionVector
     * @return ItemUseResult
     */
    public function onClickAir(Player $player, Vector3 $directionVector, array &$returnedItems): ItemUseResult
    {
        if ($player instanceof HCFPlayer) {
            $session = $player->getSession();

            if ($player->getCurrentClaim() === '§5Citadel§c'){
                $player->sendMessage(TextFormat::colorize('&cYou can\'t use this in &5Citadel &cclaim.'));
                return ItemUseResult::FAIL();
            }

            if ($session->getCooldown('enderpearl') !== null) {
                $player->sendMessage(TextFormat::colorize('&cYou have cooldown enderpearl'));
                return ItemUseResult::FAIL();
            }
            $result = parent::onClickAir($player, $directionVector, $returnedItems);
            
            if ($result)
                $session->addCooldown('enderpearl', '&l&eEnderpearl&r&7: &r&c', 15);
            Loader::$enderPearl["lastUse"][$player->getName()] = $player->getPosition();
            Loader::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($player): void {
                if ($player->isOnline()) {
                    Loader::$enderPearl["lastUse"][$player->getName()] = "";
                }
            }), 20 * 20);
            return $result;
        }
		return parent::onClickAir($player, $directionVector, $returnedItems);
	}

    #[Pure] public static function getLastHit(Player $player){
        if(isset(Loader::$enderPearl["lastUse"][$player->getName()])){
            return Loader::$enderPearl["lastUse"][$player->getName()];
        }
        return false;
    }
}