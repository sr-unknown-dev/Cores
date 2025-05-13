<?php

namespace hcf\abilities;

use hcf\abilities\entity\BallOfRangeEntity;
use hcf\abilities\entity\Cerdito1Entity;
use hcf\abilities\entity\Cerdito2Entity;
use hcf\abilities\entity\Cerdito3Entity;
use hcf\abilities\entity\FrezzerGunEntity;
use hcf\abilities\entity\PortableBardEntity;
use hcf\abilities\entity\RangeEntity;
use hcf\abilities\entity\SwitcherEntity;
use hcf\abilities\items\AbilityDisabler;
use hcf\abilities\items\BallOfRange;
use hcf\abilities\items\Berserk;
use hcf\abilities\items\ComboAbility;
use hcf\abilities\items\EffectDisabler;
use hcf\abilities\items\ExoticBone;
use hcf\abilities\items\Firework;
use hcf\abilities\items\FocusMode;
use hcf\abilities\items\FrezzerGun;
use hcf\abilities\items\GrapplinHook;
use hcf\abilities\items\JumpBoost;
use hcf\abilities\items\NinjaStar;
use hcf\abilities\items\PartnerPackages;
use hcf\abilities\items\PocketBard;
use hcf\abilities\items\PortableBard;
use hcf\abilities\items\PortablePigs;
use hcf\abilities\items\PortableRogue;
use hcf\abilities\items\PotionRefill;
use hcf\abilities\items\Regeneration;
use hcf\abilities\items\Resistance;
use hcf\abilities\items\ReverseNinja;
use hcf\abilities\items\RickyMode;
use hcf\abilities\items\Samurai;
use hcf\abilities\items\SecondChance;
use hcf\abilities\items\Speed;
use hcf\abilities\items\Strength;
use hcf\abilities\items\Switcher;
use hcf\abilities\items\Thor;
use hcf\abilities\items\TimeWarp;
use hcf\Loader;
use hcf\vkit\vKitListener;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\world\World;

class AbilitiesManager
{
    public function __construct()
    {
        # Register handler
        Loader::getInstance()->getServer()->getCommandMap()->register('HCF', new AbilitiesCommand());

        Loader::getInstance()->getServer()->getPluginManager()->registerEvents(new Switcher(), Loader::getInstance());
        Loader::getInstance()->getServer()->getPluginManager()->registerEvents(new TimeWarp(), Loader::getInstance());
        Loader::getInstance()->getServer()->getPluginManager()->registerEvents(new ExoticBone(), Loader::getInstance());
        Loader::getInstance()->getServer()->getPluginManager()->registerEvents(new EffectDisabler(), Loader::getInstance());
        Loader::getInstance()->getServer()->getPluginManager()->registerEvents(new PortableBard(), Loader::getInstance());
        Loader::getInstance()->getServer()->getPluginManager()->registerEvents(new FrezzerGun(), Loader::getInstance());
        Loader::getInstance()->getServer()->getPluginManager()->registerEvents(new SecondChance(), Loader::getInstance());
        Loader::getInstance()->getServer()->getPluginManager()->registerEvents(new AbilityDisabler(), Loader::getInstance());
        Loader::getInstance()->getServer()->getPluginManager()->registerEvents(new PartnerPackages(), Loader::getInstance());
        Loader::getInstance()->getServer()->getPluginManager()->registerEvents(new JumpBoost(), Loader::getInstance());
        Loader::getInstance()->getServer()->getPluginManager()->registerEvents(new Regeneration(), Loader::getInstance());
        Loader::getInstance()->getServer()->getPluginManager()->registerEvents(new Resistance(), Loader::getInstance());
        Loader::getInstance()->getServer()->getPluginManager()->registerEvents(new Speed(), Loader::getInstance());
        Loader::getInstance()->getServer()->getPluginManager()->registerEvents(new Strength(), Loader::getInstance());
        Loader::getInstance()->getServer()->getPluginManager()->registerEvents(new BallOfRange(), Loader::getInstance());
        Loader::getInstance()->getServer()->getPluginManager()->registerEvents(new Firework(), Loader::getInstance());
        Loader::getInstance()->getServer()->getPluginManager()->registerEvents(new Berserk(), Loader::getInstance());
        Loader::getInstance()->getServer()->getPluginManager()->registerEvents(new NinjaStar(), Loader::getInstance());
        Loader::getInstance()->getServer()->getPluginManager()->registerEvents(new Samurai(), Loader::getInstance());
        Loader::getInstance()->getServer()->getPluginManager()->registerEvents(new FocusMode(), Loader::getInstance());
        Loader::getInstance()->getServer()->getPluginManager()->registerEvents(new PotionRefill(), Loader::getInstance());
        Loader::getInstance()->getServer()->getPluginManager()->registerEvents(new RickyMode(), Loader::getInstance());
        Loader::getInstance()->getServer()->getPluginManager()->registerEvents(new ComboAbility(), Loader::getInstance());
        Loader::getInstance()->getServer()->getPluginManager()->registerEvents(new ReverseNinja(), Loader::getInstance());
        Loader::getInstance()->getServer()->getPluginManager()->registerEvents(new GrapplinHook(), Loader::getInstance());
        Loader::getInstance()->getServer()->getPluginManager()->registerEvents(new PortablePigs(), Loader::getInstance());
        Loader::getInstance()->getServer()->getPluginManager()->registerEvents(new PortableRogue(), Loader::getInstance());
        Loader::getInstance()->getServer()->getPluginManager()->registerEvents(new Thor(), Loader::getInstance());

        EntityFactory::getInstance()->register(SwitcherEntity::class, function (World $world, CompoundTag $nbt): SwitcherEntity {
            return new SwitcherEntity(EntityDataHelper::parseLocation($nbt, $world), null, $nbt);
        }, ['SwitcherEntity']);

        EntityFactory::getInstance()->register(Cerdito1Entity::class, function (World $world, CompoundTag $nbt): Cerdito1Entity {
            return new Cerdito1Entity(EntityDataHelper::parseLocation($nbt, $world), null, $nbt);
        }, ['Cerdito1Entity']);

        EntityFactory::getInstance()->register(Cerdito2Entity::class, function (World $world, CompoundTag $nbt): Cerdito2Entity {
            return new Cerdito2Entity(EntityDataHelper::parseLocation($nbt, $world), null, $nbt);
        }, ['Cerdito2Entity']);

        EntityFactory::getInstance()->register(Cerdito3Entity::class, function (World $world, CompoundTag $nbt): Cerdito3Entity {
            return new Cerdito3Entity(EntityDataHelper::parseLocation($nbt, $world), null, $nbt);
        }, ['Cerdito3Entity']);

        EntityFactory::getInstance()->register(BallOfRangeEntity::class, function (World $world, CompoundTag $nbt): BallOfRangeEntity {
            return new BallOfRangeEntity(EntityDataHelper::parseLocation($nbt, $world), null, $nbt);
        }, ['BallOfRangeEntity']);

        EntityFactory::getInstance()->register(PortableBardEntity::class, function(World $world, CompoundTag $nbt): PortableBardEntity{
            return new PortableBardEntity(EntityDataHelper::parseLocation($nbt,$world));
        },["PortableBardEntity"]);

        EntityFactory::getInstance()->register(FrezzerGunEntity::class, function (World $world, CompoundTag $nbt): FrezzerGunEntity {
            return new FrezzerGunEntity(EntityDataHelper::parseLocation($nbt, $world), null, $nbt);
        }, ['FrezzerGunEntity']);
    }

}