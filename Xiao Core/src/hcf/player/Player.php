<?php

declare(strict_types=1);

namespace hcf\player;

use hcf\faction\Faction;
use hcf\faction\FactionManager;
use hcf\cooldown\Cooldown;
use pocketmine\world\Position;
use pocketmine\math\Vector3;
use hcf\module\enchantment\Enchantment;
use hcf\Loader;
use hcf\handler\kit\classes\ClassFactory;
use hcf\handler\kit\classes\HCFClass;
use hcf\Server\Rally;
use hcf\session\Session;
use hcf\timer\types\TimerCustom;
use hcf\timer\types\TimerFFA;
use hcf\utils\time\Timer;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\entity\Location;
use pocketmine\network\mcpe\protocol\SetActorDataPacket;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataFlags;
use pocketmine\player\Player as BasePlayer;
use pocketmine\player\PlayerInfo;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\GameRulesChangedPacket;
use pocketmine\network\mcpe\protocol\types\BoolGameRule;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use pocketmine\network\mcpe\protocol\types\entity\PropertySyncData;
use DateTime;
use DateTimeZone;
use hcf\StaffMode\Chat;
use hcf\StaffMode\Good;
use hcf\timer\types\TimerAirdrop;
use hcf\timer\types\TimerMystery;
use hcf\StaffMode\Staff;
use hcf\StaffMode\Vanish;
use hcf\timer\types\TimerFreeKits;

/**
 * Class Player
 * @package hcf\player
 */
class Player extends BasePlayer
{

    public bool $scoreboardMode = true;
    private int $titleId = 0;

    /** @var int|float  */
    private int|float $lastCheck = -1;

    protected $movementTime = 0;

    /** @var int */
    private int $lastLine = 0;

    /** @var PlayerScoreboard */
    private PlayerScoreboard $scoreboard;

    private ?HCFClass $class = null;

    /** @var string|null */
    private ?string $currentClaim = null;

    /** @var bool */
    private bool $god = false;

    private int $placeholderId = 0;

    /**
     * Player construct.
     * @param Server $server
     * @param NetworkSession $session
     * @param PlayerInfo $thisInfo
     * @param bool $authenticated
     * @param Location $spawnLocation
     * @param CompoundTag|null $namedtag
     */
    public function __construct(Server $server, NetworkSession $session, PlayerInfo $thisInfo, bool $authenticated, Location $spawnLocation, ?CompoundTag $namedtag)
    {
        parent::__construct($server, $session, $thisInfo, $authenticated, $spawnLocation, $namedtag);
        $this->scoreboard = new PlayerScoreboard($this);
    }

    public function getScoreboardMode(): bool
    {
        return $this->scoreboardMode;
    }

    /**
     * @return PlayerScoreboard
     */
    public function getScoreboard(): PlayerScoreboard
    {
        return $this->scoreboard;
    }

    public function getClass(): ?HCFClass
    {
        return $this->class;
    }

    public function setClass(?HCFClass $class): void
    {
        $this->class = $class;
    }

    /**
     * @return string|null
     */
    public function getCurrentClaim(): ?string
    {
        return $this->currentClaim;
    }

    /**
     * @return bool
     */
    public function isGod(): bool
    {
        return $this->god;
    }

    /**
     * @return Session|null
     */
    public function getSession(): ?Session
    {
        return Loader::getInstance()->getSessionManager()->getSession($this->getXuid());
    }

    /**
     * @param string|null $claimName
     */
    public function setCurrentClaim(?string $claimName = null): void
    {
        $this->currentClaim = $claimName;
    }

    /**
     * @param bool $value
     */
    public function setGod(bool $value): void
    {
        $this->god = $value;
    }

    public function setScoreboardMode($mode = true): void
    {
        $this->scoreboardMode = $mode;
    }

    public function join(): void
    {
        # Scoreboard setup
        $this->scoreboard->init();

        # Scoretag & Nametag setup
        if ($this->getSession()->getFaction() !== null) {
            $dtr = Loader::getInstance()->getFactionManager()->getFaction($this->getSession()->getFaction());
            $this->setNameTag(TextFormat::colorize('&7[&c' . $this->getSession()->getFaction() . " &7| &c" . $dtr->getDtr() . " &7]\n&c" . $this->getName()));
        } else {
            $this->setNameTag(TextFormat::colorize('&c' . $this->getName()));
        }

        if ($this->getSession()->getFaction() !== null) {
            $faction = Loader::getInstance()->getFactionManager()->getFaction($this->getSession()->getFaction());
            $faction->announce(TextFormat::colorize('&aMember online: &f' . $this->getName()));
        }

        # Disconnected
        $disconnectedManager = Loader::getInstance()->getDisconnectedManager();
        $disconnected = $disconnectedManager->getDisconnected($this->getXuid());

        if ($disconnected !== null)
            $disconnected->join($this);

        # Add coordinates
        $pk = GameRulesChangedPacket::create([
            'showCoordinates' => new BoolGameRule(true, false)
        ]);
        $this->getNetworkSession()->sendDataPacket($pk);

        # Mob
        if ($this->getSession()->isMobKilled()) {
            $this->getSession()->setMobKilled(false);
            $this->getInventory()->clearAll();
            $this->getArmorInventory()->clearAll();
            $this->getEffects()->clear();
            $this->setHealth($this->getMaxHealth());
            $this->teleport($this->getWorld()->getSafeSpawn());
        }

        # Logout
        if ($this->getSession()->isLogout())
            $this->getSession()->setLogout(false);
    }

    private function updateScoreboard(): void
    {
        $this->scoreboard->updateTitles();
        if ($this->getScoreboardMode() === false) {
            if ($this->scoreboard->isSpawned()) $this->scoreboard->remove();
            return;
        }
        $this->scoreboard->updateTitles();
        // $lines = [ TextFormat::colorize(Loader::getInstance()->getConfig()->get('scoreboard.placeholder'))
        // ];
        $lines[] = TextFormat::colorize('💋');
        $name = $this->getName();
        $online = count(array_filter(Server::getInstance()->getOnlinePlayers()));
        $current = Server::getInstance()->getTicksPerSecond();
        $lines[] = TextFormat::colorize('💋');

        $god = Loader::getInstance()->getStaffModeManager()->isGod($this) ?  " §l§7×§r &aGood: §aEnable": "";
        $lines[] = TextFormat::colorize('💋');
        $lines[] = TextFormat::colorize($god);
        $lines[] = TextFormat::colorize('💋');

        # StaffMode
        $lines[] = TextFormat::colorize('💋');
        if (Loader::getInstance()->getStaffModeManager()->isStaff($this)) {
            $lines[] = TextFormat::colorize(" §l§7×§r §l&aStaffMode&r&7:&r");
            $staffchat = Loader::getInstance()->getStaffModeManager()->isStaffChat($this) ? ' §l§7×§r &aStaffChat: §aStaffTeam' : ' §l§7×§r &aStaffChat: §cPublic';
            $lines[] = TextFormat::colorize($staffchat);
            $vanish = Loader::getInstance()->getStaffModeManager()->isVanish($this) ? ' §l§7×§r &aVanish: §aEnable' : ' §l§7×§r &aVanish: §cDisable';
            $lines[] = TextFormat::colorize($vanish);
            $lines[] = TextFormat::colorize(" §l§7×§r &aOnline: &f" . $online);
            $lines[] = TextFormat::colorize('💋');
        }

        # Claims

        if ($this->getCurrentClaim() !== null) {
            $currentClaim = Loader::getInstance()->getClaimManager()->getClaim($this->getCurrentClaim());
            if ($currentClaim !== null) {
                $claimName = $this->getCurrentClaim();

                if ($currentClaim->getType() === 'spawn') {
                    $claimName = $this->getCurrentClaim();
                } elseif ($currentClaim->getType() === 'road') {
                    $claimName = '&g' . $this->getCurrentClaim();
                } elseif ($currentClaim->getType() === 'koth') {
                    $claimName = '&4KoTH ' . $this->getCurrentClaim();
                }
                $lines[] = TextFormat::colorize(' §l§7×§r &aClaim&r&7: &a' . $claimName);
            }
        } else {

            $lines[] = TextFormat::colorize(' §l§7×§r &aClaim&r&7: &f' . ($this->getPosition()->distance($this->getWorld()->getSafeSpawn()) > 400 ? '§9Wilderness' : '§fWarzone'));
        }

        # Events

        if (($sotw = Loader::getInstance()->getTimerManager()->getSotw())->isActive())
            $lines[] = TextFormat::colorize(' ' . $sotw->getFormat() . Timer::format($sotw->getTime()));
        if (($points = Loader::getInstance()->getTimerManager()->getPoints())->isActive())
            $lines[] = TextFormat::colorize(' ' . $points->getFormat() . Timer::format($points->getTime()));
        if (($death = Loader::getInstance()->getTimerManager()->getDeath())->isActive())
            $lines[] = TextFormat::colorize(' ' . $death->getFormat() . Timer::format($death->getTime()));
        if (($eotw = Loader::getInstance()->getTimerManager()->getEotw())->isActive())
            $lines[] = TextFormat::colorize(' ' . $eotw->getFormat() . Timer::format($eotw->getTime()));
        if (($purge = Loader::getInstance()->getTimerManager()->getPurge())->isActive())
            $lines[] = TextFormat::colorize(' ' . $purge->getFormat() . Timer::format($purge->getTime()));
        foreach (Loader::getInstance()->getTimerManager()->getCustomTimers() as $name => $timer) {
            if (!$timer instanceof TimerCustom) return;
            if (($custom = Loader::getInstance()->getTimerManager()->getCustomTimerByName($name))->isActive())
                $lines[] = TextFormat::colorize(' ' . $custom->getFormat() . Timer::format($custom->getTime()));
        }

        # Koth
        if (($kothName = Loader::getInstance()->getKothManager()->getKothActive()) !== null) {
            $koth = Loader::getInstance()->getKothManager()->getKoth($kothName);

            if ($koth !== null) {
                $lines[] = TextFormat::colorize(' §l§7×§r &4' . $koth->getName() . '&r&f: &r&e' . Timer::format($koth->getProgress()));
                $line[] = TextFormat::colorize(" §l§7×§r &7(" . $koth->getCoords() . "&7)");
            }
        }
        # Others

        if (($keyall = Loader::getInstance()->getTimerManager()->getKeyAll())->isEnable())
            $lines[] = TextFormat::colorize(' §l§7×§r §aKeyAll§r§f: §c' . Timer::format($keyall->getTime()));
        if (($keyallop = Loader::getInstance()->getTimerManager()->getKeyAllOP())->isEnable())
            $lines[] = TextFormat::colorize(' §l§7×§r §3KeyAllOp§r§f: §c' . Timer::format($keyallop->getTime()));
        if (($packages = Loader::getInstance()->getTimerManager()->getPackage())->isEnable())
            $lines[] = TextFormat::colorize(' §l§7×§r §5PkgAll§r§f: §c' . Timer::format($packages->getTime()));
        if (($loobox = Loader::getInstance()->getTimerManager()->getLoobox())->isEnable())
            $lines[] = TextFormat::colorize(' §l§7×§r §9BoxAll§r§f: §c' . Timer::format($loobox->getTime()));
        if (TimerAirdrop::isEnable()) {
            $lines[] = TextFormat::colorize(' §l§7×§r §3AirdropAll§r§f: §c' . Timer::format(TimerAirdrop::getTime()));
        }
        if (TimerFreeKits::isEnable()) {
            $lines[] = TextFormat::colorize(' §l§7×§r §3FreeKits§r§f: §c' . Timer::format(TimerFreeKits::getTime()));
        }
        if (TimerMystery::isEnable()) {
            $lines[] = TextFormat::colorize(' §l§7×§r §4Mystery§r§f: §c' . Timer::format(TimerMystery::getTime()));
        }
        if (TimerFFA::isEnable()) {
            $lines[] = TextFormat::colorize(' §l§7×§r §aFFA§r§f: §c' . Timer::format(TimerMystery::getTime()));
        }

        # Cooldowns
        foreach ($this->getSession()->getCooldowns() as $cooldown) {
            if ($cooldown->isVisible())
                $lines[] = TextFormat::colorize('' . $cooldown->getFormat() . Timer::format($cooldown->getTime()));
        }

        foreach ($this->getSession()->getEnergies() as $energy) {
            $lines[] = TextFormat::colorize('§7' . $energy->getFormat() . ($energy->getEnergy() . '.0'));
        }

        # Faction
        if ($this->getSession()->getFaction() !== null) {
            $faction = Loader::getInstance()->getFactionManager()->getFaction($this->getSession()->getFaction());

            # Focus
            if ($faction->getFocus() !== null) {
                if (($targetFaction = Loader::getInstance()->getFactionManager()->getFaction($faction->getFocus())) !== null) {
                    $lines[] = TextFormat::colorize('💋');
                    $lines[] = TextFormat::colorize(' §l§7×§r §l&aFocus&r&7: &f' . $targetFaction->getName());
                    $lines[] = TextFormat::colorize(' §l§7×§r §l&aHome&r&7: &f' . ($targetFaction->getHome() !== null ? $targetFaction->getHome()->getFloorX() . ', ' . $targetFaction->getHome()->getFloorZ() : 'Has no home'));
                    if ($targetFaction->getDtr() < $targetFaction->getMaxDtr()) {
                        $lines[] = TextFormat::colorize(' §l§7×§r §l&aDTR&r&7: &c' . $targetFaction->getDtr());
                    } else {
                        $lines[] = TextFormat::colorize(' §l§7×§r §l&aDTR&r&7: &a' . $targetFaction->getDtr());
                    }
                    $lines[] = TextFormat::colorize(' §l§7×§r §l&aOnline&r&7: &f' . count($targetFaction->getOnlineMembers()) . "/" . count($targetFaction->getMembers()));
                }
            }

            # Rally
            if (($rally = $faction->getRally()) !== null) {
                $pos = $this->getPosition();
                $x = (int)$pos->getX();
                $z = (int)$pos->getZ();
                $faction = Loader::getInstance()->getFactionManager()->getFaction($this->getSession()->getFaction());
                if ($faction->getOnlineMembers()){
                    $lines[] = TextFormat::colorize('💋');
                    $lines[] = TextFormat::colorize(' §l§7×§r &aRally&r&7: &7' . $rally[0]);
                    $lines[] = TextFormat::colorize(' §l§7×§r &aCoords&r&7: &7(' . $x. ', '.$z.'&7)');
                }
            }
        }
        $lines[] = TextFormat::colorize('');
        $placeholders = Loader::getInstance()->getConfig()->get('scoreboard.placeholder');

        if (is_array($placeholders)) {
            if ($this->placeholderId >= count($placeholders)) {
                $this->placeholderId = 0;
            }
            $placeholderString = $placeholders[$this->placeholderId];
            $this->placeholderId++;
        } else {
            $placeholderString = $placeholders;
        }

        $lines[] = TextFormat::colorize('&7' . $placeholderString);


        if (count($lines) === 4) {
            if ($this->scoreboard->isSpawned()) $this->scoreboard->remove();
            return;
        }

        if (!$this->scoreboard->isSpawned())
            $this->scoreboard->init();
        else $this->scoreboard->clear();

        foreach ($lines as $line => $content)
            $this->scoreboard->addLine($content . ' ');
    }

    /**
     * @param int $currentTick
     */
    public function onUpdate(int $currentTick): bool
    {
        $update = parent::onUpdate($currentTick);
        
        if ($update) {
            if ($currentTick % 20 === 0) {

                # Update custom enchants
                foreach ($this->getArmorInventory()->getContents() as $armor) {
                    foreach ($armor->getEnchantments() as $enchantment) {
                        $type = $enchantment->getType();

                        if ($type instanceof Enchantment)
                            $type->giveEffect($this);
                    }
                }
                
                # Update scoreboard
                $this->updateScoreboard();
                
                # Update invisibility 
                $this->loadInvisibility();

                if ($this->getClass() !== null)
                    $this->getClass()->onRun($this);
                else {
                    foreach(ClassFactory::getClasses() as $class) {
                        if ($class->isActive($this)) {
                            $this->class = $class;
                            break;
                        }
                    }
                }
            }
            
            if ($currentTick % 40 === 0) {
                
                # Update last line
                if ($this->lastLine >= 2) {
                    $this->lastLine = 0;
                } else {
                    $this->lastLine++;
                }
            }
        }
        return $update;
    }

    public function loadInvisibility(): void
    {
        if (!$this->getEffects()->has(VanillaEffects::INVISIBILITY()))
            return;
        $metadata = clone $this->getNetworkProperties();
        $metadata->setGenericFlag(EntityMetadataFlags::INVISIBLE, false);
        $pk2 = new SetActorDataPacket();
        $pk2->syncedProperties = new PropertySyncData([], []);
        $pk2->actorRuntimeId = $this->getId();
        $pk2->metadata = $metadata->getAll();

        foreach ($this->getViewers() as $viewer) {
            if ($viewer instanceof self) {
                if ($viewer->getSession()->getFaction() === null)
                    continue;

                if ($viewer->getSession()->getFaction() === $this->getSession()->getFaction())
                    $viewer->getNetworkSession()->sendDataPacket($pk2);
            }
        }
    }

    protected function processMostRecentMovements(): void
    {
        $micro = microtime(true);

        if ($micro - $this->lastCheck > 1) {
            $this->lastCheck = $micro;

            foreach ($this->getArmorInventory()->getContents() as $armor) {
                foreach ($armor->getEnchantments() as $enchantment) {
                    $type = $enchantment->getType();

                    if ($type instanceof Enchantment)
                        $type->handleMove($this);
                }
            }
        }
        parent::processMostRecentMovements();
    }

    public function setMovementTime($movementTime)
    {
        $this->movementTime = $movementTime;
    }

    public function isMovementTime(): bool
    {
        return (time() - $this->movementTime) < 0;
    }

    /**
     * @return string
     */
    public function getViewPos(): string
    {
        $deg = $this->getLocation()->getYaw() - 90;
        $deg = round($deg); // Redondear el valor antes de convertirlo a entero
        $deg %= 360;
        if ($deg < 0)
            $deg += 360;

        if (22.5 <= $deg && $deg < 157.5)
            return "N";
        elseif (157.5 <= $deg && $deg < 202.5)
            return "E";
        elseif (202.5 <= $deg && $deg < 337.5)
            return "S";
        else
            return "W";
    }
}
