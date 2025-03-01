<?php

declare(strict_types=1);

namespace hub\player;

use hub\cooldown\Cooldown;
use hub\handler\kit\classes\ClassFactory;
use hub\handler\kit\classes\HCFClass;
use hub\Loader;
use hub\module\enchantment\Enchantment;
use hub\session\Session;
use hub\StaffMode\Chat;
use hub\StaffMode\Good;
use hub\StaffMode\Staff;
use hub\StaffMode\Vanish;
use hub\timer\types\TimerAirdrop;
use hub\timer\types\TimerCustom;
use hub\timer\types\TimerFFA;
use hub\timer\types\TimerFreeKits;
use hub\timer\types\TimerMystery;
use hub\utils\time\Timer;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\entity\Location;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\GameRulesChangedPacket;
use pocketmine\network\mcpe\protocol\SetActorDataPacket;
use pocketmine\network\mcpe\protocol\types\BoolGameRule;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataFlags;
use pocketmine\network\mcpe\protocol\types\entity\PropertySyncData;
use pocketmine\player\Player as BasePlayer;
use pocketmine\player\PlayerInfo;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

/**
 * Class Player
 * @package hub\player
 */
class Player extends BasePlayer
{

    public bool $scoreboardMode = true;
    private int $titleId = 0;

    /** @var int|float */
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
            $faction = $this->getSession()->getFaction();
            $FName = $faction ? Loader::getInstance()->getFactionManager()->getFaction($faction)->getName() : "";

            $data = [];
            foreach (Loader::getInstance()->getFactionManager()->getFactions() as $name => $factionObj) {
                if (!in_array($factionObj->getName(), ['Spawn', 'North Road', 'South Road', 'East Road', 'West Road', 'Nether Spawn', 'End Spawn'])) {
                    $data[$name] = $factionObj->getPoints();
                }
            }
            arsort($data);
            $topFactions = array_slice($data, 0, 3, true);

            $position = "";
            $factionPosition = array_search($FName, array_keys($topFactions)) + 1;
            if ($factionPosition >= 1 && $factionPosition <= 3) {
                $position = $factionPosition;
            }
            $dtr = Loader::getInstance()->getFactionManager()->getFaction($this->getSession()->getFaction());
            $this->setNameTag(TextFormat::colorize("&c" . $this->getName() . "\n&7&8[&a#".$position."&8] &r&c" . $this->getSession()->getFaction() . " &7| &c" . $dtr->getDtr() . " &7]"));
        } else {
            $this->setNameTag(TextFormat::colorize('&c' . $this->getName()));
        }

        if ($this->getSession()->getFaction() !== null) {
            $faction = Loader::getInstance()->getFactionManager()->getFaction($this->getSession()->getFaction());
            $faction->announce(TextFormat::colorize('&aMember online: &f' . $this->getName()));
        }

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
        $lines[] = TextFormat::colorize('💋');
        $name = $this->getName();
        $online = count(array_filter(Server::getInstance()->getOnlinePlayers()));
        $current = Server::getInstance()->getTicksPerSecond();
        $lines[] = TextFormat::colorize('💋');

        $god = Loader::getInstance()->getStaffModeManager()->isGod($this) ? " §l§7×§r &aGood: §aEnable" : "";
        $lines[] = TextFormat::colorize('💋');
        $lines[] = TextFormat::colorize($god);
        $lines[] = TextFormat::colorize('💋');

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
                    foreach (ClassFactory::getClasses() as $class) {
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
