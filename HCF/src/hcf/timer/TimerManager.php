<?php

declare(strict_types=1);

namespace hcf\timer;

use hcf\timer\command\EotwCommand;
use hcf\timer\command\PurgeCommand;
use hcf\timer\command\SotwCommand;
use hcf\timer\command\x2PointsCommand;
use hcf\timer\command\DeathCommand;
use hcf\timer\command\KeyallCommand;
use hcf\timer\command\KeyallopCommand;
use hcf\timer\command\LooboxCommand;
use hcf\timer\command\PkgallCommand;
use hcf\timer\types\TimerCustom;
use hcf\timer\types\TimerKey;
use hcf\timer\types\TimerKeyOP;
use hcf\timer\types\TimerLoobox;
use hcf\timer\types\TimerPackages;
use hcf\timer\types\TimerEotw;
use hcf\timer\types\TimerDeath;
use hcf\timer\types\TimerPurge;
use hcf\timer\types\TimerSotw;
use hcf\timer\types\Timerx2Points;
use hcf\Loader;
use hcf\timer\command\AirdropCommand;
use hcf\timer\command\CustomTimerCommand;
use hcf\timer\command\FreeKitsCommand;
use hcf\timer\command\MysteryCommand;
use hcf\timer\types\TimerAirdrop;
use hcf\timer\types\TimerFreeKits;
use hcf\timer\types\TimerMystery;

/**
 * Class TimerManager
 * @package hcf\timer
 */
class TimerManager
{

    private TimerSotw $sotw;
    private TimerEotw $eotw;

    private TimerPurge $purge;
    
    private Timerx2Points $points;
    
    private TimerDeath $death;

    private TimerKey $keyall;

    private TimerAirdrop $airdrop;

    private TimerMystery $mystery;

    private TimerKeyOP $keyallop;

    private TimerPackages $packages;

    private TimerLoobox $loobox;

    private TimerFreeKits $freekits;
    
    private array $customTimers = [];
    
    /**
     * TimerManager construct.
     */
    public function __construct()
    {
        # Setup main events
        $this->sotw = new TimerSotw;
        $this->eotw = new TimerEotw;
        $this->purge = new TimerPurge;
        $this->points = new Timerx2Points;
        $this->death = new TimerDeath;
        $this->keyall = new TimerKey;
        $this->mystery = new TimerMystery;
        $this->airdrop = new TimerAirdrop;
        $this->keyallop = new TimerKeyOP;
        $this->packages = new TimerPackages;
        $this->loobox = new TimerLoobox;
        $this->freekits = new TimerFreeKits;
        
        # Register commands
        Loader::getInstance()->getServer()->getCommandMap()->register('HCF', new EotwCommand());
        Loader::getInstance()->getServer()->getCommandMap()->register('HCF', new DeathCommand());
        Loader::getInstance()->getServer()->getCommandMap()->register('HCF', new x2PointsCommand());
        Loader::getInstance()->getServer()->getCommandMap()->register('HCF', new PurgeCommand());
        Loader::getInstance()->getServer()->getCommandMap()->register('HCF', new CustomTimerCommand());
        Loader::getInstance()->getServer()->getCommandMap()->register('HCF', new KeyallCommand());
        Loader::getInstance()->getServer()->getCommandMap()->register('HCF', new AirdropCommand());
        Loader::getInstance()->getServer()->getCommandMap()->register('HCF', new MysteryCommand());
        Loader::getInstance()->getServer()->getCommandMap()->register('HCF', new KeyallopCommand());
        Loader::getInstance()->getServer()->getCommandMap()->register('HCF', new PkgallCommand());
        Loader::getInstance()->getServer()->getCommandMap()->register('HCF', new LooboxCommand());
        Loader::getInstance()->getServer()->getCommandMap()->register('HCF', new FreeKitsCommand());
        # Register listener
        Loader::getInstance()->getServer()->getPluginManager()->registerEvents(new TimerListener(), Loader::getInstance());
    }
    
    /**
     * @return TimerSotw
     */

    
    public function getPoints(): Timerx2Points
    {
        return $this->points;
    }

    public function getFreeKits(): TimerFreeKits
    {
        return $this->freekits;
    }

    
    public function getDeath(): TimerDeath
    {
        return $this->death;
    }
    
    /**
     * @return TimerEotw
     */
    public function getEotw(): TimerEotw
    {
        return $this->eotw;
    }

    public function getPurge(): TimerPurge
    {
        return $this->purge;
    }
    
    public function getKeyAll(): TimerKey
    {
        return $this->keyall;
    }

    public function getAirdropAll(): TimerAirdrop
    {
        return $this->airdrop;
    }

    public function getMysteryAll(): TimerMystery
    {
        return $this->mystery;
    }

    public function getKeyAllOP(): TimerKeyOP
    {
        return $this->keyallop;
    }

    public function getPackage(): TimerPackages
    {
        return $this->packages;
    }

    public function getLoobox(): TimerLoobox
    {
        return $this->loobox;
    }
    public function getCustomTimers(): array {
        return $this->customTimers;
    }

    public function getCustomTimerByName(string $name): TimerCustom {
        return $this->customTimers[$name];
    }

    public function addCustomTimer(string $name): void {
        $this->customTimers[$name] = new TimerCustom($name);
    }

    public function removeCustomTimer(string $name): void {
        unset($this->customTimers[$name]);
    }

    public function hasCustomTimer(string $name): bool {
        return isset($this->customTimers[$name]);
    }

    public function getSotw(): TimerSotw
    {
        return $this->sotw;
    }

}