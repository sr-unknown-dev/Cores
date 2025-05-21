<?php

namespace hcf\handler\kit\classes;

class ClassScoreboard
{
    public static $miner = [];
    private static $archer = [];
    private static $bard = [];
    private static $rogue = [];
    private static $mage = [];

    /**
     * @param string $playerName
     */
    public static function setArcher(string $playerName): void
    {
        self::$archer[] = $playerName;
    }

    /**
     * @param string $playerName
     */
    public static function setBard(string $playerName): void
    {
        self::$bard[] = $playerName;
    }

    /**
     * @param string $playerName
     */
    public static function setMage(string $playerName): void
    {
        self::$mage[] = $playerName;
    }

    /**
     * @param string $playerName
     */
    public static function setMiner(string $playerName): void
    {
        self::$miner[] = $playerName;
    }

    /**
     * @param string $playerName
     */
    public static function setRogue(string $playerName): void
    {
        self::$rogue[] = $playerName;
    }

    public static function removeArcher(string $playerName)
    {
        unset(self::$archer[$playerName]);
    }

    public static function removeBard(string $playerName)
    {
        unset(self::$bard[$playerName]);
    }

    public static function removeRogue(string $playerName)
    {
        unset(self::$rogue[$playerName]);
    }

    public static function removeMiner(string $playerName)
    {
        unset(self::$miner[$playerName]);
    }

    public static function removeMage(string $playerName)
    {
        unset(self::$mage[$playerName]);
    }


    public static function isArcher(string $playerName)
    {
        return in_array($playerName, self::$archer);
    }

    public static function isBard(string $playerName)
    {
        return in_array($playerName, self::$bard);
    }

    public static function isMage(string $playerName)
    {
        return in_array($playerName, self::$mage);
    }

    public static function isMiner(string $playerName)
    {
        return in_array($playerName, self::$miner);
    }

    public static function isRogue(string $playerName)
    {
        return in_array($playerName, self::$rogue);
    }
}