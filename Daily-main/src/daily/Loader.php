<?php

namespace daily;

use pocketmine\plugin\PluginBase;
use daily\Command\DailyCommand;
use daily\Command\DailyEditCommand;
use daily\Cooldowns\Cooldown;
use daily\Task\Tasks;
use daily\Utils\Npc;
use daily\Utils\Utils;
use muqsit\invmenu\InvMenuHandler;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\utils\SingletonTrait;
use pocketmine\world\World;

class Loader extends PluginBase {

    use SingletonTrait;
    public $cooldowns;

    public function onLoad():void{self::setInstance($this);}
    public function onEnable(): void {
        if(!InvMenuHandler::isRegistered()){
            InvMenuHandler::register($this);
        }
        $this->getServer()->getCommandMap()->register("daily", new DailyCommand($this));
        $this->getServer()->getCommandMap()->register("daily", new DailyEditCommand($this));
        $this->getServer()->getPluginManager()->registerEvents(new Events(), $this);
        $this->getScheduler()->scheduleRepeatingTask(new Tasks($this), 20);
        EntityFactory::getInstance()->register(Npc::class, function (World $world, CompoundTag $nbt): Npc {
            return new Npc(EntityDataHelper::parseLocation($nbt, $world), Npc::parseSkinNBT($nbt), $nbt);
        }, ['Npc']);
    }
}