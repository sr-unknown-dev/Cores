<?php

declare(strict_types=1);

namespace hcf\faction;

use hcf\Loader;
use hcf\player\Player;
use hcf\session\Session;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use pocketmine\world\Position;

class Faction
{

    /** @var string[] */
    private static array $types = [
        'Spawn' => 'spawn',
        'North Road' => 'road',
        'South Road' => 'road',
        'East Road' => 'road',
        'West Road' => 'road',
    ];

    /** @var string */
    const LEADER = 'leader';
    /** @var string */
    const CO_LEADER = 'co-leader';
    /** @var string */
    const CAPTAIN = 'captain';
    /** @var string */
    const MEMBER = 'member';

    /** @var string */
    private string $name;

    /** @var int */
    private int $balance;
    /** @var int */
    private int $points;
    /** @var int */
    private int $kothCaptures;
    /** @var int */
    private int $strikes;
    /** @var float */
    private float $dtr;
    
    /** @var bool */
    private bool $raidable;
    /** @var bool */
    private bool $upgradeSpeed;
    /** @var bool */
    private bool $upgradeResistance;
    /** @var bool */
    private bool $upgradeStregth;
    /** @var bool */
    private bool $upgradeJump;
    
    /** @var string[] */
    private array $roles;

    /** @var string|null */
    private ?string $focus = null;
    /** @var array|null */
    private ?array $rally = null;
    /** @var int|null */
    private ?int $timeRegeneration;
    /** @var Position|null */
    private ?Position $home = null;

    /**
     * Faction construct.
     * @param string $name
     * @param array $data
     */
    public function __construct(string $name, array $data)
    {
        $this->name = $name;
        $this->roles = $data['roles'];
        $this->dtr = (float)$data['dtr'];
        $this->balance = (int)$data['balance'];
        $this->points = (int)$data['points'];
        $this->kothCaptures = (int)$data['kothCaptures'];
        $this->strikes = intval($data['strikes'] ?? 0);
        $this->timeRegeneration = (int)$data['timeRegeneration'];
        $this->raidable = $data['raided'] ?? false;
        $this->upgradeSpeed = $data['upgradeSpeed'] ?? false;
        $this->upgradeResistance = $data['upgradeResistance'] ?? false;
        $this->upgradeStregth = $data['upgradeStregth'] ?? false;
        $this->upgradeJump = $data['upgradeJump'] ?? false;

        if ($data['home'] !== null)
            $this->home = new Position((int)$data['home']['x'], (int)$data['home']['y'], (int)$data['home']['z'], Loader::getInstance()->getServer()->getWorldManager()->getWorldByName($data['home']['world']));

        if ($data['claim'] !== null) {
            $type = self::$types[$name] ?? 'faction';
            Loader::getInstance()->getClaimManager()->createClaim($name, $type, $data['claim']['minX'], $data['claim']['maxX'], $data['claim']['minZ'], $data['claim']['maxZ'], $data['claim']['world']);
        }
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @param string $member
     * @return string|null
     */
    public function getRole(string $member): ?string
    {
        return $this->roles[$member] ?? null;
    }

    /**
     * @return float
     */
    public function getDtr(): float
    {
        return $this->dtr;
    }

    /**
     * @return float
     */
    public function getMaxDtr(): float
    {
        return 0.01 + (count($this->getMembers()) * 1.00);
    }

    /**
     * @return int
     */
    public function getBalance(): int
    {
        return $this->balance;
    }

    /**
     * @return int
     */
    public function getPoints(): int
    {
        return $this->points;
    }

    /**
     * @return int
     */
    public function getKothCaptures(): int
    {
        return $this->kothCaptures;
    }

    /**
     * @return int
     */
    public function getStrikes(): int
    {
        return $this->strikes;
    }


    /**
     * @return bool
     */
    public function isRaidable(): bool
    {
        return $this->raidable;
    }
    
    /**
     * @return bool
     */
    public function isSpeedUpgrade(): bool
    {
        return $this->upgradeSpeed;
    }
    
    /**
     * @return bool
     */
    public function isResistanceUpgrade(): bool
    {
        return $this->upgradeResistance;
    }
    
    /**
     * @return bool
     */
    public function isStrengthUpgrade(): bool
    {
        return $this->upgradeStregth;
    }
    
    /**
     * @return bool
     */
    public function isJumpUpgrade(): bool
    {
        return $this->upgradeJump;
    }

    /**
     * @return string|null
     */
    public function getFocus(): ?string
    {
        return $this->focus;
    }

    /**
     * @return array|null
     */
    public function getRally(): ?array
    {
        return $this->rally;
    }

    /**
     * @return Position|null
     */
    public function getHome(): ?Position
    {
        return $this->home;
    }

    /**
     * @return int|null
     */
    public function getTimeRegeneration(): ?int
    {
        return $this->timeRegeneration;
    }

    /**
     * @param string $member
     * @param string $role
     */
    public function addRole(string $member, string $role): void
    {
        $this->roles[$member] = $role;
    }

    /**
     * @param string $member
     */
    public function removeRole(string $member): void
    {
        unset($this->roles[$member]);
    }

    /**
     * @param float $value
     */
    public function setDtr(float $value): void
    {
        $this->dtr = $value;
    }

    /**
     * @param int $value
     */
    public function setBalance(int $value): void
    {
        $this->balance = $value;
    }

    /**
     * @param int $value
     */
    public function setPoints(int $value): void
    {
        $this->points = $value;
    }

    /**
     * @param int $value
     */
    public function setKothCaptures(int $value): void
    {
        $this->kothCaptures = $value;
    }

    /**
     * @param int $value
     */
    public function setStrikes(int $value): void
    {
        $this->strikes = $value;
    }
    
    /**
     * @param bool $value
     */
    public function setRaidable(bool $value): void
    {
        $this->raidable = $value;
    }
    
    /**
     * @param bool $value
     */
    public function setUpgradeSpeed(bool $value): void
    {
        $this->upgradeSpeed = $value;
    }
    
    /**
     * @param bool $value
     */
    public function setUpgradeResistance(bool $value): void
    {
        $this->upgradeResistance = $value;
    }
    
    /**
     * @param bool $value
     */
    public function setUpgradeStrength(bool $value): void
    {
        $this->upgradeStregth = $value;
    }
    
    /**
     * @param bool $value
     */
    public function setUpgradeJump(bool $value): void
    {
        $this->upgradeJump = $value;
    }

    /**
     * @param string|null $value
     */
    public function setFocus(?string $value): void
    {
        $this->focus = $value;
    }

    /**
     * @param array|null $value
     */
    public function setRally(?array $value): void
    {
        $this->rally = $value;
    }

    /**
     * @param Position|null $value
     */
    public function setHome(?Position $value): void
    {
        $this->home = $value;
    }

    /**
     * @param int|null $value
     */
    public function setTimeRegeneration(?int $value): void
    {
        $this->timeRegeneration = $value;
    }

    /**
     * @return Session[]
     */
    public function getMembers(): array
    {
        return array_filter(Loader::getInstance()->getSessionManager()->getSessions(), function (Session $session): bool {
            return $session->getFaction() !== null && $session->getFaction() === $this->getName();
        });
    }

    /**
     * @return Player[]
     */
    public function getOnlineMembers(): array
    {
        return array_filter(Server::getInstance()->getOnlinePlayers(), function (\pocketmine\player\Player $player): bool {
            return $player instanceof Player && $player->getSession()->getFaction() === $this->getName();
        });
    }

    /**
     * @return Session[]
     */
    public function getMembersByRole(string $role): array
    {
        return array_filter($this->getMembers(), function (Session $session) use ($role): bool {
            return $this->getRole($session->getXuid()) === $role;
        });
    }

    /**
     * @param string $message
     */
    public function announce(string $message): void
    {
        foreach ($this->getOnlineMembers() as $member) {
            $member->sendMessage($message);
        }
    }

    public function disband(): void
    {
        foreach ($this->getMembers() as $member) {
            $member->setFaction(null);
            $member->setFactionChat(false);
        }
        Loader::getInstance()->getClaimManager()->removeClaim($this->getName());
    }

    public function onUpdate(): void
    {
        if ($this->isSpeedUpgrade() === true) {
            foreach ($this->getOnlineMembers() as $member) {
                $claim = Loader::getInstance()->getClaimManager()->getClaim($this->getName());
                if ($claim !== null) {
                    if ($claim->inside($member->getPosition())) {
                        $member->getEffects()->add(new EffectInstance(VanillaEffects::SPEED(), 20 * 7, 1));
                    }
                }
            }
        }
        if ($this->isResistanceUpgrade() === true) {
            foreach ($this->getOnlineMembers() as $member) {
                $claim = Loader::getInstance()->getClaimManager()->getClaim($this->getName());
                if ($claim !== null) {
                    if ($claim->inside($member->getPosition())) {
                        $member->getEffects()->add(new EffectInstance(VanillaEffects::RESISTANCE(), 20 * 7, 0));
                    }
                }
            }
        }
        if ($this->isStrengthUpgrade() === true) {
            foreach ($this->getOnlineMembers() as $member) {
                $claim = Loader::getInstance()->getClaimManager()->getClaim($this->getName());
                if ($claim !== null) {
                    if ($claim->inside($member->getPosition())) {
                        $member->getEffects()->add(new EffectInstance(VanillaEffects::STRENGTH(), 20 * 7, 0));
                    }
                }
            }
        }
        if ($this->isJumpUpgrade() === true) {
            foreach ($this->getOnlineMembers() as $member) {
                $claim = Loader::getInstance()->getClaimManager()->getClaim($this->getName());
                if ($claim !== null) {
                    if ($claim->inside($member->getPosition())) {
                        $member->getEffects()->add(new EffectInstance(VanillaEffects::JUMP_BOOST(), 20 * 7, 1));
                    }
                }
            }
        }
        if ($this->getTimeRegeneration() !== null) {
            if (Loader::getInstance()->getConfig()->get('facion.regeneration.offline', true) === false && count($this->getOnlineMembers()) === 0)
                return;
            $this->timeRegeneration--;

            if ($this->timeRegeneration === -1) {
                $this->timeRegeneration = null;
                $this->setDtr(0.1 + (count($this->getMembers()) * 1.00));
                $this->setRaidable(false);

                # Setup scoretag for team members
                foreach ($this->getOnlineMembers() as $member){
                    $faction = $member->getSession()->getFaction();
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
                    $dtr = Loader::getInstance()->getFactionManager()->getFaction($member->getSession()->getFaction());
                    $member->setNameTag(TextFormat::colorize("&c" . $member->getName() . "\n&r&7[&c" . $member->getSession()->getFaction() . " &7| &c" . $dtr->getDtr() . " &7]"));
                }
            }
        }
    }


    /**
     * @return array
     */
    public function getData(): array
    {
        $data = [
            'roles' => $this->getRoles(),
            'dtr' => $this->getDtr(),
            'balance' => $this->getBalance(),
            'points' => $this->getPoints(),
            'kothCaptures' => $this->getKothCaptures(),
            'strikes' => $this->getStrikes(),
            'timeRegeneration' => $this->getTimeRegeneration(),
            'raided' => $this->isRaidable(),
            'upgradeSpeed' => $this->isSpeedUpgrade(),
            'upgradeResistance' => $this->isResistanceUpgrade(),
            'upgradeStregth' => $this->isStrengthUpgrade(),
            'upgradeJump' => $this->isJumpUpgrade(),
            'home' => null,
            'claim' => null
        ];

        if ($this->getHome() !== null) {
            $data['home'] = [
                'x' => $this->getHome()->getFloorX(),
                'y' => $this->getHome()->getFloorY(),
                'z' => $this->getHome()->getFloorZ(),
                'world' => $this->getHome()->getWorld()->getFolderName()
            ];
        }

        if (($claim = Loader::getInstance()->getClaimManager()->getClaim($this->getName())) !== null) {
            $data['claim'] = [
                'minX' => $claim->getMinX(),
                'maxX' => $claim->getMaxX(),
                'minZ' => $claim->getMinZ(),
                'maxZ' => $claim->getMaxZ(),
                'world' => $claim->getWorld()
            ];
        }
        return $data;
    }
}
