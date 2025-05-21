<?php

declare(strict_types=1);

namespace hcf;

use CortexPE\DiscordWebhookAPI\Message;
use CortexPE\DiscordWebhookAPI\Webhook;
use hcf\anticheat\DataBase;
use hcf\entity\default\EnderpearlEntity;
use hcf\item\Fireworks;
use hcf\player\Player;
use hcf\StaffMode\Chat;
use hcf\Tasks\ChatTask;
use partneritems\type\SurprisePresent;
use pocketmine\block\tile\Sign;
use pocketmine\block\Water;
use pocketmine\entity\Location;
use pocketmine\event\block\PlayerBucketEmptyEvent;
use pocketmine\event\entity\EntityDamageByChildEntityEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityItemPickupEvent;
use pocketmine\event\entity\ProjectileLaunchEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerBucketEmptyEvent as PlayerPlayerBucketEmptyEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerCreationEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemConsumeEvent;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\item\Bucket;
use pocketmine\item\EnderPearl;
use pocketmine\item\FlintSteel;
use pocketmine\item\Hoe;
use pocketmine\item\ItemTypeIds;
use pocketmine\item\Shovel;
use pocketmine\item\Tool;
use pocketmine\item\VanillaItems;
use pocketmine\utils\TextFormat;
use pocketmine\world\sound\ThrowSound;

class HCFListener implements Listener
{

    /**
     * @param EntityDamageEvent $event
     * @priority HIGH
     */
    public function handleDamage(EntityDamageEvent $event): void
    {
        $cause = $event->getCause();
        $entity = $event->getEntity();

        if ($entity instanceof Player) {
            if ($event->isCancelled()) return;

            if ($entity->getSession()->getCooldown('pvp.timer')) {
                $event->cancel();
                return;
            }

            if ($entity->getSession()->getCooldown('pvp.timer') !== null) {
                if ($cause === EntityDamageEvent::CAUSE_ENTITY_ATTACK || $cause === EntityDamageEvent::CAUSE_PROJECTILE) {
                    $event->cancel();
                    return;
                }
            }

            if ($entity->getCurrentClaim() === 'Spawn') {
                $event->cancel();
                return;
            }

            if ($event instanceof EntityDamageByEntityEvent || $event instanceof EntityDamageByChildEntityEvent) {
                $damager = $event->getDamager();

                if ($damager instanceof Player) {
                    if ($damager->getSession()->getCooldown('pvp.timer') !== null || $damager->getSession()->getCooldown('pvp.timer') !== null) {
                        $event->cancel();
                        return;
                    }

                    if ($damager->getCurrentClaim() === 'Spawn') {
                        $event->cancel();
                        return;
                    }

                    if ($entity->getSession()->getFaction() !== null && $damager->getSession()->getFaction() !== null) {
                        if ($entity->getSession()->getFaction() === $damager->getSession()->getFaction()) {
                            $damager->sendMessage(TextFormat::colorize('&eYou cannot hurt §3' . $entity->getName() . '§e.'));
                            $event->cancel();
                            return;
                        }
                    }
                    $entity->getSession()->addCooldown('lastDamage', '', 15, false, false);

                    $entity->getSession()->addCooldown('spawn.tag', ' §l§7×§r §l&cCombat&r&f: &r&c', 25);
                    $damager->getSession()->addCooldown('spawn.tag', ' §l§7×§r §l&cCombat&r&f: &r&c', 30);
                }
            }
        }
    }

    /**
     * @param EntityItemPickupEvent $event
     */
    public function handlePickupItem(EntityItemPickupEvent $event): void
    {
        $entity = $event->getEntity();
        $origin = $event->getOrigin();

        if ($entity instanceof Player) {
            $session = Loader::getInstance()->getSessionManager()->getSession($entity->getXuid());
            $owningEntity = $origin->getOwningEntity();

            if ($session->getCooldown('pvp.timer') !== null || $session->getCooldown('pvp.timer') !== null) {
                if ($owningEntity === null || $owningEntity->getId() !== $entity->getId()) {
                    $entity->sendTip(TextFormat::colorize("§7You are in §cPvP Timer"));
                    $event->cancel();
                }
            }
        }
    }

    /**
     * @param PlayerChatEvent $event
     */
    public function handleChat(PlayerChatEvent $event): void
    {
        $player = $event->getPlayer();
        $message = $event->getMessage();

        if ($player instanceof Player) {
            if ($player->getSession()->getFaction() !== null && $player->getSession()->hasFactionChat()) {
                $faction = Loader::getInstance()->getFactionManager()->getFaction($player->getSession()->getFaction());

                if ($faction !== null) {
                    $event->cancel();

                    foreach ($faction->getOnlineMembers() as $member) {
                    $member->sendMessage(TextFormat::colorize('&8[&gFac&8] §a' . $player->getName() . '§f:§7 ' . $message));
                    return;
                    }
                }
            }
        }
    }

    /**
     * @param PlayerCreationEvent $event
     */
    public function handleCreation(PlayerCreationEvent $event): void
    {
        $event->setPlayerClass(Player::class);
    }

    /**
     * @param PlayerDeathEvent $event
     */
    public function handleDeath(PlayerDeathEvent $event): void
    {
        /** @var Player */
        $player = $event->getPlayer();

        if (!$player instanceof Player)
            return;
        $last = $player->getLastDamageCause();

        $killerXuid = null;
        $killer = null;
        $itemInHand = null;
        $message = '';

        if ($last instanceof EntityDamageByEntityEvent || $last instanceof EntityDamageByChildEntityEvent) {
            $damager = $last->getDamager();

            if ($damager instanceof Player) {
                $killerXuid = $damager->getXuid();
                $killer = $damager->getName();
                $itemInHand = $damager->getInventory()->getItemInHand();

                $damager->getSession()->addKill();
                $damager->getSession()->addKillStreak();

                if (Loader::getInstance()->getTimerManager()->getDeath()->isActive()) {
                    $damagerName = $damager->getName();
                    $hcf = Loader::getInstance()->getHandlerManager()->getCrateManager()->getCrate("hcf");
                    $hcf->giveKey($player, 10);
                }

                if ($damager->getSession()->getKillStreak() > $damager->getSession()->getHighestKillStreak())
                    $damager->getSession()->addHighestKillStreak();

                if ($damager->getSession()->getFaction() !== null) {
                    $faction = Loader::getInstance()->getFactionManager()->getFaction($damager->getSession()->getFaction());
                    if (Loader::getInstance()->getTimerManager()->getPoints()->isActive()) {
                        $faction->setPoints($faction->getPoints() + 2);
                    } else {
                        $faction->setPoints($faction->getPoints() + 1);
                    }
                }
            }
        }

        if ($player->getSession()->getCooldown('spawn.tag') !== null)
            $player->getSession()->removeCooldown('spawn.tag');
        $spawnClaim = Loader::getInstance()->getClaimManager()->getClaim('Spawn');

        if ($spawnClaim !== null && $spawnClaim->getType() === 'spawn')
            $player->setCurrentClaim($spawnClaim->getName());
        $player->getSession()->addDeath();
        $player->getSession()->setKillStreak(0);
        $player->getSession()->addCooldown('pvp.timer', ' §l§7×§r §l§aPvP Timer&r&f: &r&c', 60 * 60);


        $player->getSession()->addDeath();
        if ($player->getSession()->getFaction() !== null) {
            $faction = Loader::getInstance()->getFactionManager()->getFaction($player->getSession()->getFaction());

            $faction->setPoints($faction->getPoints() - 1);
            $faction->setDtr($faction->getDtr() - 1.0);
            $faction->announce(TextFormat::colorize('&7Member Death: &g' . $player->getName() . "\n" . '&7DTR: &g' . $faction->getDtr()));

            # Faction Raid
            if ($faction->getDtr() < 0.00 && !$faction->isRaidable()) {
                $faction->setRaidable(true);
                $faction->setPoints($faction->getPoints() - 5);

                if ($killerXuid !== null) {
                    $session = Loader::getInstance()->getSessionManager()->getSession($killerXuid);

                    if ($session !== null && $session->getFaction()) {
                        $fac = Loader::getInstance()->getFactionManager()->getFaction($session->getFaction());

                        if ($fac !== null) {
                            $fac->setPoints($fac->getPoints() + 15);
                        }

                        foreach (Loader::getInstance()->getServer()->getOnlinePlayers() as $players) {
                            $players->sendMessage(TextFormat::colorize('&cThe faction &l' . $faction->getName() . ' &r&cis now Rideable!'));
                        }
                    }
                }
            }



            # Regen time
            if (!$faction->isRaidable()) {
                $faction->setTimeRegeneration(8 * 60);
            } else {
                $regenTime = $faction->getTimeRegeneration();
                $value = $regenTime + (5 * 60);

                $faction->setTimeRegeneration($value < 8 * 60 ? $value : 8 * 60);
            }

            # Setup scoretag for team members
            if ($faction->getDtr() <= 0.00 && $faction->isRaidable()) {
                foreach ($faction->getOnlineMembers() as $member) {
                    $dtr = Loader::getInstance()->getFactionManager()->getFaction($member->getSession()->getFaction());
                    $member->setNameTag(TextFormat::colorize("&c" . $member->getName() . "\n&r&7[&c" . $member->getSession()->getFaction() . " &7| &c" . $dtr->getDtr() . " &7]"));
                }
            }else{
                foreach ($faction->getOnlineMembers() as $member) {
                    $dtr = Loader::getInstance()->getFactionManager()->getFaction($member->getSession()->getFaction());
                    $member->setNameTag(TextFormat::colorize("&c" . $member->getName() . "\n&r&7[&c" . $member->getSession()->getFaction() . " &7| &c" . $dtr->getDtr() . " &7]"));
                }
            }
        }



        if ($killer === null) {
            $message = '&c' . $player->getName() . '&4[' . $player->getSession()->getKills() . '] &edied';
            $webhook = $player->getName() . '[' . $player->getSession()->getKills() . '] died';
        } else {
            if (!$itemInHand->isNull() && $itemInHand instanceof Tool) {
                $message = '&c' . $player->getName() . '&4[' . $player->getSession()->getKills() . '] &ewas slain by &9' . $killer . '&4[' . Loader::getInstance()->getSessionManager()->getSession($killerXuid)->getKills() . '] &cusing ' . $itemInHand->getName();
            } else {
                $message = '&c' . $player->getName() . '&4[' . $player->getSession()->getKills() . '] &ewas slain by &9' . $killer . '&4[' . Loader::getInstance()->getSessionManager()->getSession($killerXuid)->getKills() . ']';
            }
            $webhook = '' . $player->getName() . '[' . $player->getSession()->getKills() . '] was slain by ' . $killer . '[' . Loader::getInstance()->getSessionManager()->getSession($killerXuid)->getKills() . ']';
        }
        # Construct a discord webhook with its URL
        $webHook = new Webhook(Loader::getInstance()->getConfig()->get('kills.webhook'));

        # Construct a new Message object
        $msg = new Message();
        $msg->setContent($webhook);
        $webHook->send($msg);

        $event->setDeathMessage(TextFormat::colorize($message));
    }

    /**
     * @param PlayerExhaustEvent $event
     */
    public function handleExhaust(PlayerExhaustEvent $event): void
    {
        $player = $event->getPlayer();

        if ($player instanceof Player) {
            if ($player->getCurrentClaim() !== null) {
                $claim = Loader::getInstance()->getClaimManager()->getClaim($player->getCurrentClaim());

                if ($claim !== null && $claim->getType() === 'spawn') {
                    $event->cancel();

                    if ($player->getHungerManager()->getFood() !== $player->getHungerManager()->getMaxFood())
                        $player->getHungerManager()->setFood($player->getHungerManager()->getMaxFood());
                    return;
                }
            }

            if ($player->getSession()->hasAutoFeed()) {
                $event->cancel();

                if ($player->getHungerManager()->getFood() !== $player->getHungerManager()->getMaxFood())
                    $player->getHungerManager()->setFood($player->getHungerManager()->getMaxFood());
                return;
            }
        }
    }

    /**
     * @param PlayerInteractEvent $event
     * @priority HIGHEST
     */
    public function handleInteract(PlayerInteractEvent $event)
    {
        $action = $event->getAction();
        $block = $event->getBlock();
        $player = $event->getPlayer();
        $item = $event->getItem();
        $name = $player->getName();

        if (!$player instanceof Player)
            return;

        if ($player->getPosition()->distance($player->getServer()->getWorldManager()->getDefaultWorld()->getSafeSpawn()->asVector3()) < 170) {
            if ($item instanceof Bucket) {
                $event->cancel();
                return;
            }

            if ($item->getNamedTag()->getTag('pp_packages') !== null) {
                return;
            }

            if ($item->getNamedTag()->getTag('mystery_box') !== null) {
                return;
            }

            if ($item->getNamedTag()->getTag('airdrop_item') !== null) {
                return;
            }

            if ($block instanceof Water) {
                $event->cancel();
                return;
            }

            if ($item instanceof Fireworks) {
                $event->cancel();
                return;
            }

            if ($item instanceof Shovel) {
                $event->cancel();
                return;
            }

            if ($item instanceof Hoe) {
                $event->cancel();
                return;
            }

            if ($block instanceof Sign) {
                $event->cancel();
                return;
            }
        }

        if ($item instanceof FlintSteel) {
            $event->cancel();
            return;
        }

        if ($event instanceof PlayerPlayerBucketEmptyEvent) {
            $claim = Loader::getInstance()->getClaimManager()->insideClaim($block->getPosition());

            if ($claim === null) {
                if ($block->getPosition()->distance($player->getServer()->getWorldManager()->getDefaultWorld()->getSafeSpawn()->asVector3()) < 400) {
                    $event->cancel();
                    return;
                }
            }

            if ($player->getInventory()->getItemInHand()->equals(VanillaItems::WATER_BUCKET(), false, false)) {
                $event->cancel();
                $player->sendMessage(TextFormat::colorize('§8[§4!§8] §7You cannot place blocks in this area'));
                return;
            }

            if (in_array($claim->getType(), ['spawn', 'road', 'koth', 'citadel'])) {
                $event->cancel();
                $player->sendMessage(TextFormat::colorize('&cYou cannot place blocks in this area'));
                return;
            }
        }
    }

    /**
     * @param PlayerItemConsumeEvent $event
     */
    public function handleItemConsume(PlayerItemConsumeEvent $event): void
    {
        /** @var Player */
        $player = $event->getPlayer();
        $item = $event->getItem();

        if ($player instanceof Player)

            if ($event->isCancelled())
                return;

        if ($item->getTypeId() == ItemTypeIds::GOLDEN_APPLE) {
            if ($player->getSession()->getCooldown('apple') !== null) {
                $event->cancel();
                return;
            }
            $player->getSession()->addCooldown('apple', '§6Apple: &r&c', 15);
        } elseif ($item->getTypeId() == ItemTypeIds::ENCHANTED_GOLDEN_APPLE) {
            if ($player->getSession()->getCooldown('apple.enchanted') !== null) {
                $event->cancel();
                return;
            }
            $player->getSession()->addCooldown('apple.enchanted', '§gGapple&r&7: &r&c', 3600);
        }
    }

    /**
     * @param PlayerItemUseEvent $event
     */
    public function handleItemUse(PlayerItemUseEvent $event): void
    {
        $item = $event->getItem();
        $player = $event->getPlayer();
    }

    /**
     * @param PlayerJoinEvent $event
     */
    public function handleJoin(PlayerJoinEvent $event): void
    {
        /** @var Player */
        $player = $event->getPlayer();
        $player->join();

        $joinMessage = str_replace('{player}', $player->getName(), Loader::getInstance()->getConfig()->get('join.message'));
        $event->setJoinMessage(TextFormat::colorize($joinMessage));
    }

    /**
     * @param PlayerLoginEvent $event
     */
    public function handleLogin(PlayerLoginEvent $event): void
    {
        $player = $event->getPlayer();
        $session = Loader::getInstance()->getSessionManager()->getSession($player->getXuid());

        if ($session === null)
            Loader::getInstance()->getSessionManager()->addSession($player->getXuid(), [
                'name' => $player->getName(),
                'faction' => null,
                'balance' => 0,
                'crystals' => 0,
                'cooldowns' => [],
                'energies' => [],
                'stats' => [
                    'kills' => 0,
                    'deaths' => 0,
                    'killStreak' => 0,
                    'highestKillStreak' => 0
                ]
            ]);
        else {
            if ($player->getName() !== $session->getName())
                $session->setName($player->getName());
        }
    }

    /**
     * @param PlayerQuitEvent $event
     */
    public function handleQuit(PlayerQuitEvent $event): void
    {
        /** @var Player */
        $player = $event->getPlayer();

        if (!$player instanceof Player)
            return;
        $quitMessage = str_replace('{player}', $player->getName(), Loader::getInstance()->getConfig()->get('quit.message'));
        $disconnectedManager = Loader::getInstance()->getDisconnectedManager();

        if ($player->getSession() !== null && $player->getSession()->getFaction() !== null) {
            $faction = Loader::getInstance()->getFactionManager()->getFaction($player->getSession()->getFaction());
            $faction->announce(TextFormat::colorize("&cMember offline: &f" . $player->getName() . "\n&cDTR: &f" . $faction->getDtr()));
        }

        /*if (isset(Chat::$chat[$player->getName()])) {
            unset(Chat::$chat[$player->getName()]);
        }*/

        if ($player->getSession() !== null && !$player->getSession()->isLogout()) {
            if ($player->getCurrentClaim() !== null) {
                $claim = Loader::getInstance()->getClaimManager()->getClaim($player->getCurrentClaim());
            } else {
                $disconnectedManager->addDisconnected($player);
                if ($disconnectedManager->getDisconnected($player->getXuid()) === null) {
                    $disconnectedManager->addDisconnected($player);
                }
            }
        }
        $event->setQuitMessage(TextFormat::colorize($quitMessage));
    }

    public function handlePlayerItemUse(PlayerItemUseEvent $event): void
    {
        $player = $event->getPlayer();
        $item = $event->getItem();
        if(!$player instanceof Player) return;
        if($item instanceof EnderPearl){
            $event->cancel();
            $session = $player->getSession();
            if ($player->getCurrentClaim() === 'Citadel'){
                $player->sendTip(TextFormat::colorize('&cYou can\'t use this in Citadel &cclaim.'));
                return;
            }

            if ($session->getCooldown('enderpearl') !== null) {
                $player->sendTip(TextFormat::colorize('&gYou have cooldown enderpearl'));
                $event->cancel();
                return;
            }
            $location = $player->getLocation();

            $projectile = new EnderpearlEntity(Location::fromObject($player->getEyePos(), $player->getWorld(), $location->yaw, $location->pitch), $player);
            $projectile->setMotion($player->getDirectionVector()->multiply($item->getThrowForce()));

            $projectileEv = new ProjectileLaunchEvent($projectile);
            $projectileEv->call();
            if($projectileEv->isCancelled()){
                $projectile->flagForDespawn();
                return;
            }

            $projectile->spawnToAll();

            $location->getWorld()->addSound($location, new ThrowSound());
            $session->addCooldown('enderpearl', ' §l§7×§r §3Enderpearl&7: &c', 15);
            if($item->getCount() > 1){
                $item->setCount($item->getCount() - 1);
            } else {
                $item = VanillaItems::AIR();
            }
            $player->getInventory()->setItemInHand($item);
        }
    }

}