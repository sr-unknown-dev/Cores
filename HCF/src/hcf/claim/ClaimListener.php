<?php

declare(strict_types=1);

namespace hcf\claim;

use hcf\entity\EnderpearlEntity;
use hcf\Loader;
use hcf\player\Player;
use hcf\Server\ClaimSe;
use pocketmine\block\Air;
use pocketmine\block\Chest;
use pocketmine\block\Door;
use pocketmine\block\EnderChest;
use pocketmine\block\FenceGate;
use pocketmine\block\tile\Sign;
use pocketmine\block\VanillaBlocks;
use pocketmine\entity\projectile\Snowball;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerBucketEmptyEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\item\EnderPearl;
use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use pocketmine\utils\TextFormat;
use pocketmine\world\Position;
use pocketmine\world\World;
use pocketmine\world\WorldException;

/**
 * Class ClaimListener
 * @package hcf\claim
 */
class ClaimListener implements Listener
{

    /** @var string */
    const DEATHBAN = '&e(&cDeathban&e)';
    /** @var string */
    const NO_DEATHBAN = '&e(&aNon-Deathban&e)';

    public function handleChat(PlayerChatEvent $event): void
    {
        /** @var Player $player */
        $player = $event->getPlayer();
        $message = $event->getMessage();
        if($message == "cancel"){
            $event->cancel();
            if (($creator = Loader::getInstance()->getClaimManager()->getCreator($player->getName())) !== null) {
                $creator->deleteCorners($player);
                Loader::getInstance()->getClaimManager()->removeCreator($player->getName());
                $player->sendMessage(TextFormat::colorize('§8[§b!§8] §cYou have canceled the claim'));
            } else
                $player->sendMessage(TextFormat::colorize('§8[§b!§8] §cYou are not in claim mode yet'));
            return;
        }
    }

    /**
     * @param BlockBreakEvent $event
     * @throws WorldException
     */
    public function handleBreak(BlockBreakEvent $event): void
    {
        /** @var Player $player */
        $player = $event->getPlayer();
        $block = $event->getBlock();
        $claim = Loader::getInstance()->getClaimManager()->insideClaim($block->getPosition());

        if ($event->isCancelled())
            return;

        if ($player->isGod())
            return;

        if ($claim === null) {
            if ($block->getPosition()->distance($player->getServer()->getWorldManager()->getDefaultWorld()->getSafeSpawn()->asVector3()) < 400)
                $event->cancel();
            return;
        }

        if (in_array($claim->getType(), ['spawn', 'road', 'koth', 'citadel', 'custom'])) {
            $event->cancel();
            $player->sendMessage(TextFormat::colorize('§8[§4!§8] §cYou cannot§cbreak blocks §cin this area'));
            return;
        }

        if (!Loader::getInstance()->getTimerManager()->getEotw()->isActive() && $player->getSession()->getFaction() !== $claim->getName()) {
            $faction = Loader::getInstance()->getFactionManager()->getFaction($claim->getName());

            if ($faction !== null && $faction->getDtr() > 0.00) {
                $event->cancel();
                $player->sendMessage(TextFormat::colorize('§8[§4!§8] §7You cannot§cbreak blocks §c in §a' . $claim->getName() . ' §cterritory'));
            }

            if ($faction !== null && $faction->getDtr() > 0.00) {
                $player->getLocation()->getYaw() + 90;
                return;
            }
        }
    }

    /**
     * @param BlockPlaceEvent $event
     * @throws WorldException
     */
    public function handlePlace(BlockPlaceEvent $event): void
    {
        /** @var Player $player */
        $player = $event->getPlayer();
        $block = $event->getBlockAgainst();
        $item = $event->getItem();
        $claim = Loader::getInstance()->getClaimManager()->insideClaim($block->getPosition());

        if ($block->getTypeId() === VanillaBlocks::TNT()->getTypeId()){
            $event->cancel();
        }

        if ($event->isCancelled())
            return;

        if ($player->isGod())
            return;

        if ($item->getNamedTag()->getTag('pp_packages') !== null) {
            return;
        }

        if ($item->getNamedTag()->getTag('mystery_box') !== null) {
            return;
        }

        if ($item->getNamedTag()->getTag('airdrop_item') !== null) {
            return;
        }

        if ($claim === null) {
            if ($block->getPosition()->distance($player->getServer()->getWorldManager()->getDefaultWorld()->getSafeSpawn()->asVector3()) < 400)
                $event->cancel();
            return;
        }

        if (in_array($claim->getType(), ['spawn', 'road', 'koth', 'citadel', 'custom'])) {
            $event->cancel();
            $player->sendMessage(TextFormat::colorize('§8[§g!§8] §cYou cannot §bplace blocks §cin this area'));
            return;
        }

        if (!Loader::getInstance()->getTimerManager()->getEotw()->isActive() && $player->getSession()->getFaction() !== $claim->getName()) {
            $faction = Loader::getInstance()->getFactionManager()->getFaction($claim->getName());

            if ($faction !== null && $faction->getDtr() > 0.00) {
                $event->cancel();
                $player->sendMessage(TextFormat::colorize('§8[§4!§8] §cYou cannot §bplace blocks §cin§c' . $claim->getName() . ' §cterritory'));
                $event->cancel();
            }
        }
    }
      /**
     * @param PlayerBucketEmptyEvent $event
     */
  public function hadleItemInterect(PlayerBucketEmptyEvent $event): void
{
    /** @var Player $player */
    $player = $event->getPlayer();
    $block = $event->getBlockClicked();
    $spawn = $player->getServer()->getWorldManager()->getDefaultWorld()->getSafeSpawn()->asVector3();

    $distanceFromSpawn = $block->getPosition()->distance($spawn);

    if ($event->isCancelled() || $player->isGod()) {
        return;
    }

    if ($player->getInventory()->getItemInHand()->equals(VanillaItems::WATER_BUCKET(), false, false) && $distanceFromSpawn < 400) {
        $event->cancel();
        $player->sendMessage(TextFormat::colorize('§8[§4!§8] §cYou cannot place &3water &cin this area.'));
        return;
    }
}

/**
 * @param EntityTeleportEvent $event
 */
public function handleTeleport(EntityTeleportEvent $event): void
{
    $entity = $event->getEntity();
    $to = $event->getTo();

    if (!$entity instanceof Player)
        return;
    $claim = Loader::getInstance()->getClaimManager()->insideClaim($to);

    if ($claim === null)
        return;

    if ($entity->getSession()->getCooldown('spawn.tag') !== null) {
        if ($claim->getType() == 'spawn') {
            $event->cancel();
            $entity->sendMessage(TextFormat::colorize('§8[§g!§8] §cYou have §pSpawn Tag. §cYou cannot teleport to this location'));
            return;
        }
    } elseif ($entity->getSession()->getCooldown('pvp.timer') !== null) {
        if ($claim->getType() === 'faction' && $entity->getSession()->getFaction() !== $claim->getName()) {
            $event->cancel();
            $entity->sendMessage(TextFormat::colorize('§8[§g!§8] §cYou have §aPvPTimer. §cYou cannot teleport to this location'));
            return;
        }
    }
        $entity->setCurrentClaim($claim->getName());
    }

    /**
     * @param PlayerDropItemEvent $event
     */
 public function handleDropItem(PlayerDropItemEvent $event): void
    {
        $player = $event->getPlayer();
        $item = $event->getItem();

        if (Loader::getInstance()->getClaimManager()->getCreator($player->getName()) !== null) {
            if ($item->getNamedTag()->getTag('claim_type'))
                $event->cancel();
        }
    }

    public function handleFall(EntityDamageEvent $event) {
        $player = $event->getEntity();
        if($event->getCause() === EntityDamageEvent::CAUSE_FALL) {
            if ($player instanceof Player)
            if ($player->getCurrentClaim() === 'Spawn') {
                $event->cancel();
            }
        }
    }

    public function ItemUse(PlayerItemUseEvent $event): void {
        $player = $event->getPlayer();
        $item = $event->getItem();

        if ($player instanceof Player)
        if ($player->getCurrentClaim() === 'Spawn') {

            if ($item instanceof Snowball) {
                $event->cancel();
            }
        }

    }

    /**
     * @param PlayerInteractEvent $event
     */
    public function handleInteract(PlayerInteractEvent $event): void
    {
        $action = $event->getAction();
        $block = $event->getBlock();
        /** @var Player $player */
        $player = $event->getPlayer();
        $item = $player->getInventory()->getItemInHand();

        $claim = Loader::getInstance()->getClaimManager()->insideClaim($block->getPosition());
        # $claimn = Loader::getInstance()->getClaimManager()->getClaim();
        $tile = $player->getWorld()->getTile($block->getPosition());
        if ($claim !== null && $claim->getName() === "Bank") {
            if($block instanceof Chest || $block instanceof EnderChest){
                return;
            }
        }

        if (isset(ClaimSe::$claim[$player->getName()])) {
            if ($action === PlayerInteractEvent::LEFT_CLICK_BLOCK && $block instanceof Air && $player->isSneaking()) {
                if (($creator = Loader::getInstance()->getClaimManager()->getCreator($player->getName())) !== null) {
                    if (!$creator->isValid()) return;
                    if($creator->getType() === 'faction'){
                        $faction = Loader::getInstance()->getFactionManager()->getFaction($player->getSession()->getFaction());
                        $balance = $faction->getBalance() - $creator->calculateValue();
                        if ($balance < 0) {
                            $player->sendMessage(TextFormat::colorize('§8[§b!§8] §cYour faction does not have enough§cmoney §cto pay the claim'));
                            return;
                        }
                        $faction->setBalance($balance);
                    }
                    unset(ClaimSe::$claim[$player->getName()]);
                    $creator->deleteCorners($player);
                    Loader::getInstance()->getClaimManager()->createClaim($creator->getName(), $creator->getType(), $creator->getMinX(), $creator->getMaxX(), $creator->getMinZ(), $creator->getMaxZ(), $creator->getWorld());
                    $player->sendMessage(TextFormat::colorize('§8[§b!§8]§cYou have made the &aclaim§cthe opclaim !' . $creator->getName()));
                    Loader::getInstance()->getClaimManager()->removeCreator($player->getName());

                    foreach ($player->getInventory()->getContents() as $slot => $i) {
                        if ($i->getNamedTag()->getTag('claim_type')) {
                            $player->getInventory()->clear($slot);
                            break;
                        }
                    }
                }
            }
        }

        if ($tile instanceof Sign) {
            $text = $tile->getText();
            $lines = $text->getLines();

            if ($player->hasPermission("use.player.command")) {
                if ($lines[0] === TextFormat::colorize('&e[Elevator]')) {
                    if ($lines[1] === TextFormat::colorize('&7up')) {
                        for ($i = $block->getPosition()->getFloorY() + 1; $i < World::Y_MAX; $i++) {
                            $firstBlock = $player->getWorld()->getBlockAt($block->getPosition()->getFloorX(), $i + 1, $block->getPosition()->getFloorZ());
                            $secondBlock = $player->getWorld()->getBlockAt($block->getPosition()->getFloorX(), $i, $block->getPosition()->getFloorZ());
                            $thirdBlock = $player->getWorld()->getBlockAt($block->getPosition()->getFloorX(), $i - 1, $block->getPosition()->getFloorZ());

                            if (((($firstBlock instanceof FenceGate || $firstBlock instanceof Door) && $firstBlock->isOpen()) || !$firstBlock->isSolid()) &&
                                ((($secondBlock instanceof FenceGate || $secondBlock instanceof Door) && $secondBlock->isOpen()) || !$secondBlock->isSolid()) &&
                                $thirdBlock->isSolid()) {
                                $player->teleport(new Position($block->getPosition()->getFloorX() + 0.5, $i, $block->getPosition()->getFloorZ() + 0.5, $player->getWorld()));
                                break;
                            }
                        }
                    } elseif ($lines[1] === TextFormat::colorize('&7down')) {
                        for ($i = $block->getPosition()->getFloorY() - 1; $i >= World::Y_MIN; $i--) {
                            $firstBlock = $player->getWorld()->getBlockAt($block->getPosition()->getFloorX(), $i + 1, $block->getPosition()->getFloorZ());
                            $secondBlock = $player->getWorld()->getBlockAt($block->getPosition()->getFloorX(), $i, $block->getPosition()->getFloorZ());
                            $thirdBlock = $player->getWorld()->getBlockAt($block->getPosition()->getFloorX(), $i - 1, $block->getPosition()->getFloorZ());

                            if (((($firstBlock instanceof FenceGate || $firstBlock instanceof Door) && $firstBlock->isOpen()) || !$firstBlock->isSolid()) &&
                                ((($secondBlock instanceof FenceGate || $secondBlock instanceof Door) && $secondBlock->isOpen()) || !$secondBlock->isSolid()) &&
                                $thirdBlock->isSolid()) {
                                $player->teleport(new Position($block->getPosition()->getFloorX() + 0.5, $i, $block->getPosition()->getFloorZ() + 0.5, $player->getWorld()));
                                break;
                            }
                        }
                    }
                }
            }
        }

        if (($creator = Loader::getInstance()->getClaimManager()->getCreator($player->getName())) !== null) {
            if ($item->getNamedTag()->getTag('claim_type') !== null) {
                $event->cancel();

                if (($claim = Loader::getInstance()->getClaimManager()->insideClaim($block->getPosition())) !== null && ($claim->getType() !== 'koth' || $claim->getName() !== $creator->getName())) {
                    $player->sendMessage(TextFormat::colorize('§8[§4!§8]§c You cannot make a claim in an area that is already §aclaiming'));
                    return;
                }

                if ($creator->getType() === 'faction') {
                    if ($block->getPosition()->distance($player->getServer()->getWorldManager()->getDefaultWorld()->getSafeSpawn()->asVector3()) < 400) {
                        $player->sendMessage(TextFormat::colorize('§8§[§4!§8] §cYou can\'t§cclaim §cin this position'));
                        return;
                    }
                }

                if ($action === PlayerInteractEvent::LEFT_CLICK_BLOCK) {
                    if ($creator->getFirst() === null) {
                        $creator->calculate($block->getPosition(), $player);
                        $player->sendMessage(TextFormat::colorize('§8[§g!§8] §aYou have successfully selected the§cfirst &aposition.'));
                        $event->cancel();
                    }
                } elseif ($action === PlayerInteractEvent::RIGHT_CLICK_BLOCK) {
                    $result = $creator->calculate($block->getPosition(), $player, false);

                    if (!$result) {
                        $player->sendMessage(TextFormat::colorize('§8[§4!§8] §eThe position was not selected in the same world'));
                        Loader::getInstance()->getClaimManager()->removeCreator($player->getName());

                        foreach ($player->getInventory()->getContents() as $slot => $i) {
                            if ($i->getNamedTag()->getTag('claim_type')) {
                                $player->getInventory()->clear($slot);
                                break;
                            }
                        }
                        return;
                    }

                    if ($creator->calculateClaim($creator->getFirst(), $block->getPosition())) {
                        if ($creator->getType() === 'capzone') {
                            return;
                        }
                        $player->sendMessage(TextFormat::colorize('§8[§g!§8] §eThe position was selected in other faction'));
                        return;
                    }

                    $player->sendMessage(TextFormat::colorize('§8[§g!§8]§cYou have successfully selected the &3second §cposition.'));
                    ClaimSe::$claim[$player->getName()] = true;

                    if ($creator->getType() === 'faction') {
                        $player->sendMessage(TextFormat::colorize('§4» &cThe price of your claim is §a$' . $creator->calculateValue() . '. §cHit the pillar while you bend down to accept'));
                    }
                }
            }
        }

        if ($item instanceof EnderPearl) {
            if ($action === PlayerInteractEvent::RIGHT_CLICK_BLOCK && $block instanceof FenceGate) {
                $event->cancel();
                $session = $player->getSession();

                if ($player->getCurrentClaim() === 'Citadel') {
                    $player->sendMessage(TextFormat::colorize('§c You can\'t use this in §dCitadel §cclaim.'));
                    return;
                }
            }
        }

        if ($player->isGod())
            return;
        $claim = Loader::getInstance()->getClaimManager()->insideClaim($block->getPosition());

        if ($claim === null)
            return;

        if (!$block instanceof Sign) {
            if (!Loader::getInstance()->getTimerManager()->getEotw()->isActive() && $player->getSession()->getFaction() !== $claim->getName() && $claim->getType() !== 'spawn') {
                $faction = Loader::getInstance()->getFactionManager()->getFaction($claim->getName());

                if ($faction !== null && $faction->getDtr() > 0.00 && !Loader::getInstance()->getTimerManager()->getPurge()->isActive()) {

                    $event->cancel();

                    if ($action === PlayerInteractEvent::RIGHT_CLICK_BLOCK) {
                        if ($block instanceof FenceGate) {
                            if(Loader::getInstance()->getTimerManager()->getPurge()->isActive()) return;
                            $distance = $player->getPosition()->distance($block->getPosition());

                            if ($distance <= 3 && !$block->isOpen()) {
                                $player->setMotion($player->getDirectionVector()->multiply(-1.5));
                            }
                        }
                    }
                }
                return;
            }
        }
    }

    /**
     * @param PlayerJoinEvent $event
     */
    public function handleJoin(PlayerJoinEvent $event): void
    {
        /** @var Player $player */
        $player = $event->getPlayer();
        $claim = Loader::getInstance()->getClaimManager()->insideClaim($player->getPosition());

        if ($claim !== null)
            $player->setCurrentClaim($claim->getName());
    }

    /**
     * @param PlayerMoveEvent $event
     * @throws WorldException
     */
    public function handleMove(PlayerMoveEvent $event): void
    {
        $player = $event->getPlayer();

        $pvpTimer = $player->getSession()->getCooldown('pvp.timer');
        $pvpTimerActive = $pvpTimer !== null && !$pvpTimer->isPaused();

        if ($pvpTimerActive) {
            $playerFaction = $player->getSession()->getFaction();
        }

        if ($player->isMovementTime()) {
            $event->cancel();
            return;
        }

        $claim = Loader::getInstance()->getClaimManager()->insideClaim($player->getPosition());

        $leaving = self::DEATHBAN;
        $entering = self::DEATHBAN;

        if ($event->isCancelled()) {
            return;
        }

        if (!$this->isBorderLimit($player->getPosition())) {
            $player->teleport($this->correctPosition($player->getPosition()));
        }

        $currentClaimName = $player->getCurrentClaim();
        if ($currentClaimName !== null) {
            $currentClaim = Loader::getInstance()->getClaimManager()->getClaim($currentClaimName);
            if ($currentClaim !== null) {
                if ($currentClaim->getName() === $claim?->getName()) {
                    return;
                }

                $leavingName = '&c' . $currentClaimName;
                if ($currentClaim->getType() === 'spawn') {
                    $leaving = self::NO_DEATHBAN;
                    $leavingName = '&a' . $currentClaimName;
                    if ($pvpTimerActive) {
                        $pvpTimer->setPaused(false);
                    }
                } elseif ($currentClaim->getType() === 'road') {
                    $leavingName = '&6' . $currentClaimName;
                } elseif ($currentClaim->getType() === 'koth') {
                    $leavingName = '&9KoTH ' . $currentClaimName;
                }

                $player->sendMessage(TextFormat::colorize('&eNow leaving: ' . $leavingName . ' ' . $leaving));
            }
        }

        if ($claim === null) {
            if ($currentClaimName !== null) {
                $player->sendMessage(TextFormat::colorize('&eNow entering:&c ' . ($player->getPosition()->distance($player->getWorld()->getSafeSpawn()) > 400 ? 'Wilderness' : 'Warzone') . ' ' . $entering));
                $player->setCurrentClaim(null);
            }
            return;
        }

        $enteringName = '&c' . $claim->getName();

        if ($claim->getType() === 'spawn') {
            $entering = self::NO_DEATHBAN;
            $enteringName = '&a' . $claim->getName();
            if ($player->getSession()->getCooldown('spawn.tag') !== null) {
                $event->cancel();
                return;
            }
        } elseif ($claim->getType() === 'road') {
            $enteringName = '&6' . $claim->getName();
        } elseif ($claim->getType() === 'koth') {
            $enteringName = '&9KoTH ' . $claim->getName();
        } else {
            if ($pvpTimerActive) {

                if ($player->getSession()->getFaction() !== $claim->getName() || $player->getSession()->getFaction() === $claim->getName()) {
                    $event->cancel();
                    return;
                }
            } else {
                if ($player->getSession()->getFaction() !== $claim->getName()) {

                }
            }
        }

        $player->sendMessage(TextFormat::colorize('&eNow entering: ' . $enteringName . ' ' . $entering));
        $player->setCurrentClaim($claim->getName());
    }


    protected function isBorderLimit(Vector3 $position): bool
    {
        $border = 1300;
        return $position->getFloorX() >= -$border && $position->getFloorX() <= $border && $position->getFloorZ() >= -$border && $position->getFloorZ() <= $border;
    }

    protected function correctPosition(Vector3 $position): Vector3
    {
        $border = 1300;

        $x = $position->getFloorX();
        $y = $position->getFloorY();
        $z = $position->getFloorZ();

        $xMin = -$border;
        $xMax = $border;

        $zMin = -$border;
        $zMax = $border;

        if ($x <= $xMin) {
            $x = $xMin + 4;
        } elseif ($x >= $xMax) {
            $x = $xMax - 4;
        }
        if ($z <= $zMin) {
            $z = $zMin + 4;
        } elseif ($z >= $zMax) {
            $z = $zMax - 4;
        }
        $y = 72;
        return new Vector3($x, $y, $z);
    }

    /**
     * @param PlayerQuitEvent $event
     */
    public function handleQuit(PlayerQuitEvent $event): void
    {
        $player = $event->getPlayer();

        if (Loader::getInstance()->getClaimManager()->getCreator($player->getName()) !== null) {
            Loader::getInstance()->getClaimManager()->removeCreator($player->getName());

            foreach ($player->getInventory()->getContents() as $slot => $i) {
                if ($i->getNamedTag()->getTag('claim_type')) {
                    $player->getInventory()->clear($slot);
                    break;
                }
            }
        }
    }
}
