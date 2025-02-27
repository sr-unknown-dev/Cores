<?php

declare(strict_types=1);

namespace muqsit\invmenu;

use InvalidArgumentException;
use LogicException;
use muqsit\invmenu\session\PlayerManager;
use muqsit\invmenu\type\InvMenuTypeRegistry;
use pocketmine\plugin\Plugin;
use pocketmine\Server;

final class InvMenuHandler{

    private static ?Plugin $registrant = null;
    private static ?InvMenuTypeRegistry $type_registry = null; // Inicialización por defecto
    private static ?PlayerManager $player_manager = null; // Inicialización por defecto

    public static function register(Plugin $plugin) : void{
        if(self::isRegistered()){
            throw new InvalidArgumentException("{$plugin->getName()} attempted to register " . self::class . " twice.");
        }

        self::$registrant = $plugin;
        self::$type_registry = new InvMenuTypeRegistry();
        self::$player_manager = new PlayerManager(self::getRegistrant());
        Server::getInstance()->getPluginManager()->registerEvents(new InvMenuEventHandler(self::getPlayerManager()), $plugin);
    }

    public static function isRegistered() : bool{
        return self::$registrant instanceof Plugin;
    }

    public static function getRegistrant() : Plugin{
        if(self::$registrant === null){
            throw new LogicException("Cannot obtain registrant before registration");
        }
        return self::$registrant;
    }

    public static function getTypeRegistry() : InvMenuTypeRegistry{
        if(self::$type_registry === null){
            throw new LogicException("Cannot obtain type registry before registration");
        }
        return self::$type_registry;
    }

    public static function getPlayerManager() : PlayerManager{
        if(self::$player_manager === null){
            throw new LogicException("Cannot obtain player manager before registration");
        }
        return self::$player_manager;
    }
}