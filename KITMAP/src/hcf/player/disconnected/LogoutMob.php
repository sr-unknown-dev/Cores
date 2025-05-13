<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 *
 *
 */

declare(strict_types=1);

namespace hcf\player\disconnected;

use hcf\player\Player;
use CortexPE\DiscordWebhookAPI\Message;
use CortexPE\DiscordWebhookAPI\Webhook;
use hcf\Loader;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\entity\Villager;
use hcf\utils\time\Timer;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\Item;
use pocketmine\item\Tool;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class LogoutMob extends Villager{

    private static ?Player $player = null;
    private static $inventory;
    private static $armorinventory;
    private ?Player $lastHit = null;
    private int $time = 15;

	public static function getNetworkTypeId() : string
    { 
        return EntityIds::VILLAGER; 
    }

	public function getName() : string{
		return "Villager";
	}

	protected function initEntity(CompoundTag $nbt) : void{
		parent::initEntity($nbt);
	}

    public static function setPlayer(Player $players) {
        self::$player = $players;
    }

    public static function setInventory(array $items) {
        self::$inventory = $items;
    }

    public static function setInventoryArmor(array $items) {
        self::$armorinventory = $items;
    }

    /**
     * @return Item[]
     */
    public function getDrops(): array
    {
        $drops = [];
        $disconnected = self::$player;
        
        if ($disconnected !== null) {
            return array_merge(self::$inventory, self::$armorinventory);
        }
        return $drops;
    }

    public function onUpdate(int $currentTick): bool
    {
        $hasUpdate = parent::onUpdate($currentTick);
        $disconnected = self::$player;
        
        if ($hasUpdate) {
            if ($currentTick % 20 === 0) {
                if ($disconnected !== null) {
                    $this->time--;
                    $this->setNameTag(TextFormat::colorize('&8[§cCombat-Logger§8]§e ' . $disconnected->getName() . ' &7- &c' . Timer::convert($this->time)));
        
                    if ($this->time <= 0) {
                        Loader::getInstance()->getDisconnectedManager()->removeDisconnected($disconnected->getXuid());
                        $this->flagForDespawn();
                        return true;
                    }
                } else {
                    $this->flagForDespawn();
                    return true;
                }
            }
        }
        return $hasUpdate;
    }

    /**
     * @param EntityDamageEvent $source
     */
    public function attack(EntityDamageEvent $source): void
    {
        $cause = $source->getCause();
        $disconnected = self::$player;
        
        if ($disconnected !== null) {
            $session = $disconnected->getSession();
            
            if ($session !== null) {
                if ($cause !== EntityDamageEvent::CAUSE_ENTITY_ATTACK) {
                    $source->cancel();
                    return;
                }
                
                if ($source instanceof EntityDamageByEntityEvent) {
                    $damager = $source->getDamager();

                    if ($damager instanceof Player) {
                        if ($damager->getName() === $session->getName()) {
                            $source->cancel();
                            return;
                        }
                        
                        if ($damager->getCurrentClaim() === 'Spawn') {
                            $source->cancel();
                            return;
                        }
                    
                        if ($damager->getSession()->getCooldown('starting.timer') !== null || $damager->getSession()->getCooldown('pvp.timer') !== null) {
                            $source->cancel();
                            return;
                        }

                        if ($session->getFaction() !== null && $damager->getSession()->getFaction() !== null) {
                            if ($session->getFaction() === $damager->getSession()->getFaction()) {
                                $source->cancel();
                                return;
                            }
                        }

                        $this->lastHit = $damager;
                        $this->time = 15;
                        
                        $session->addCooldown('spawn.tag', '§l§f|§r§cSpawn Tag&r&7: &r&c', 30);
                        $damager->getSession()->addCooldown('spawn.tag', '§l§f|§r§cSpawn Tag&r&7: &r&c', 30);
                    }
                }
            }
        }
        parent::attack($source);
    }
    
    protected function onDeath(): void
    {
        parent::onDeath();
        $disconnected = self::$player;
        
        if ($disconnected === null)
            return;
        $session = $disconnected->getSession();
        $killerXuid = null;
        $killer = null;
        $itemInHand = null;
        $message = '';
        $damager = $this->lastHit;

        if ($damager instanceof Player) {
            $killerXuid = $damager->getXuid();
            $killer = $damager->getName();
            $itemInHand = $damager->getInventory()->getItemInHand();

            $damager->getSession()->addKill();
            $damager->getSession()->addKillStreak();

            if ($damager->getSession()->getKillStreak() > $damager->getSession()->getHighestKillStreak())
                $damager->getSession()->addHighestKillStreak();

            if ($damager->getSession()->getFaction() !== null) {
                $faction = Loader::getInstance()->getFactionManager()->getFaction($damager->getSession()->getFaction());
                $faction->setPoints($faction->getPoints() + 1);
            }
        }
        $session->setMobKilled(true);
        $session->removeCooldown('spawn.tag');
        $session->addDeath();
        $session->setKillStreak(0);
        $session->addCooldown('pvp.timer', '§l§f|§r §aPvPTimer&r&7: &r&c', 60 * 60, true);

        if ($session->getFaction() !== null) {
            $faction = Loader::getInstance()->getFactionManager()->getFaction($session->getFaction());
            
            $faction->setPoints($faction->getPoints() - 1);
            $faction->setDtr($faction->getDtr() - 1.0);
            $faction->announce(TextFormat::colorize('&cMember Death: &f' . $session->getName() . "\n" . '&cDTR: &f' . $faction->getDtr()));
            
            # Faction Raid
            if ($faction->getDtr() < 0.00 && !$faction->isRaidable()) {
                $faction->setRaidable(true);
                $faction->setPoints($faction->getPoints() - 10);

                if ($killerXuid !== null) {
                    $session = Loader::getInstance()->getSessionManager()->getSession($killerXuid);

                    if ($session !== null && $session->getFaction()) {
                        $fac = Loader::getInstance()->getFactionManager()->getFaction($session->getFaction());

                        if ($fac !== null) {
                            $fac->setPoints($fac->getPoints() + 3);
                            $fac->announce(TextFormat::colorize('&cThe faction &l' . $faction->getName() . ' &r&cis now Rideable!'));
                        }
                    }
                }
            }
            
            # Regen time
            if (!$faction->isRaidable()) {
               $faction->setTimeRegeneration(35 * 60);
            } else {
                $regenTime = $faction->getTimeRegeneration();
                $value = $regenTime + (5 * 60);

                $faction->setTimeRegeneration($value < 35 * 60 ? $value : 35 * 60);
            }
            
            # Setup scoretag for team members
            foreach ($faction->getOnlineMembers() as $member)
                $member->setScoreTag(TextFormat::colorize('&6[&c' . $faction->getName() . ' &c' . $faction->getDtr() . '■&6]'));
        }

        if ($killer === null) {
            $message = '&c' . $session->getName() . '&4[' . $session->getKills() . '] &edied';
            $webhook = $session->getName() . '[' . $session->getKills() . '] died';
        } else {
            if (!$itemInHand->isNull() && $itemInHand instanceof Tool) {
                $message = '&c' . $session->getName() . '&4[' . $session->getKills() . '] &ewas slain by &c' . $killer . '&4[' . Loader::getInstance()->getSessionManager()->getSession($killerXuid)->getKills() . '] &cusing ' . $itemInHand->getName();
                $webhook = '`' . $session->getName() . '[' . $session->getKills() . '] was slain by ' . $killer . '[' . Loader::getInstance()->getSessionManager()->getSession($killerXuid)->getKills() . ']`';
            } else {
                $message = '&c' . $session->getName() . '&4[' . $session->getKills() . '] &ewas slain by &c' . $killer . '&4[' . Loader::getInstance()->getSessionManager()->getSession($killerXuid)->getKills() . ']';
                $webhook = '`' . $session->getName() . '[' . $session->getKills() . '] was slain by ' . $killer . '[' . Loader::getInstance()->getSessionManager()->getSession($killerXuid)->getKills() . ']`';
            }
        }
        // Construct a discord webhook with its URL
        $webHook = new Webhook(Loader::getInstance()->getConfig()->get('kills.webhook'));

        // Construct a new Message object
        $msg = new Message();
        $msg->setContent($webhook);
        $webHook->send($msg);
        Server::getInstance()->broadcastMessage(TextFormat::colorize($message));

        // Modificar esta parte para verificar si el inventario está inicializado
        if ($disconnected->isConnected() && $disconnected->getInventory() !== null) {
            // Guardar el inventario del jugador desconectado
            Loader::getInstance()->getHandlerManager()->getRollbackManager()->saveInventory($disconnected);
        } else {
            // Manejar el caso cuando el inventario no está disponible
            Server::getInstance()->getLogger()->info("No se pudo guardar el inventario de " . $disconnected->getName() . " porque no está inicializado.");
        }
    }
}
