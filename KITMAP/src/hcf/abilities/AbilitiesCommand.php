<?php

declare(strict_types=1);

namespace hcf\abilities;

use hcf\Loader;
use hcf\utils\Inventories;
use pocketmine\block\utils\DyeColor;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\item\LegacyStringToItemParser;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;

/**
 * Class vKitCommand
 * @package juqn\hcf\vkit\command
 */
class AbilitiesCommand extends Command
{

    /** @var AbilitiesCommand[] */
    private array $subCommands = [];

    /**
     * vKitCommand construct.
     */
    public function __construct()
    {
        parent::__construct('abilities', '');
		$this->setPermission("abilities.command.use");
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        //------------------TimeWarp----------------
        $timewarp = VanillaItems::STONE_AXE();
        $timewarp->setCustomName("§r§aTime Warp");
        $timewarp->setLore([
            "§f",
            "§7Use this item and after 5s",
            "§7it will take you the last position where you pearled within 15s",
            "§a",
            "§ePurcharse at §3" . Loader::getInstance()->getConfig()->get('tebex')]);
        $timewarp->getNamedTag()->setString("Abilities","TimeWarp");
        //------------------------------------------

        //------------------ExoticBone----------------
        $exoticbone = VanillaItems::BONE();
        $exoticbone->setCustomName("§r§aAntiTrapper Bone");
        $exoticbone->setLore([
            "§f",
            "§7Hit a player 3 times in a row",
            "§7to prevent them placing/breaking blocks",
            "§7or interacting with openables for 20 seconds",
            "§a",
            "§ePurcharse at §3" . Loader::getInstance()->getConfig()->get('tebex')]);
        $exoticbone->getNamedTag()->setString("Abilities","ExoticBone");
        //------------------------------------------

        //------------------EffectDisabler----------------
        $effectdisabler = VanillaItems::SLIMEBALL();
        $effectdisabler->setCustomName("§r§aEffects Disabler");
        $effectdisabler->setLore([
            "",
            "§7hit a player with the item",
            "§7to clear the effects of the other player!",
            "§f",
            "§ePurcharse at §3" . Loader::getInstance()->getConfig()->get('tebex')]);
        $effectdisabler->getNamedTag()->setString("Abilities","EffectDisabler");
        //------------------------------------------

        //------------------PortableBard----------------
        $portalebard = VanillaItems::ZOMBIE_SPAWN_EGG()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::INFINITY(), 1));
        $portalebard->setCustomName("§r§dPortable Bard");
        $portalebard->setLore([
            "§f",
            "§7Using the spawn egg will create a witch and they hold negative effects to your enemies!",
            "§a",
            "§ePurcharse at §3" . Loader::getInstance()->getConfig()->get('tebex')]);
        $portalebard->getNamedTag()->setString("Abilities","PortableBard");
        //------------------------------------------

        //------------------FreezerGun----------------
        $frezzergun = VanillaItems::SNOWBALL();
        $frezzergun->setCustomName("§r§4Freezer Gun");
        $frezzergun->setLore([
            "§f",
            "§7When shooting an enemy, they will be completely frozen for a certain time",
            "§a",
            "§ePurcharse at §3" . Loader::getInstance()->getConfig()->get('tebex')]);
        $frezzergun->getNamedTag()->setString("Abilities","FrezzerGun");
        //------------------------------------------

        //------------------SecondChance----------------
        $secondchance = VanillaItems::GHAST_TEAR();
        $secondchance->setCustomName("§r§bSecondChance");
        $secondchance->setLore([
            "§f",
            "§7This item will remove the enderpearl cooldown",
            "§7to give you a second chance",
            "§2",
            "§ePurcharse at §3" . Loader::getInstance()->getConfig()->get('tebex')]);
        $secondchance->getNamedTag()->setString("Abilities","SecondChance");
        //------------------------------------------

        //------------------AbilityDisabler----------------
        $abilitydisabler = VanillaItems::POISONOUS_POTATO();
        $abilitydisabler->setCustomName("§r§aAbility Disabler");
        $abilitydisabler->setLore([
            "§f",
            "§7When you use this item every player within",
            "§715 blocks of you wont be able",
            "§7to use any pp item for 90 seconds",
            "§2",
            "§ePurcharse at §3" . Loader::getInstance()->getConfig()->get('tebex')]);
        $abilitydisabler->getNamedTag()->setString("Abilities","AbilityDisabler");
        //------------------------------------------

        //------------------Switcher----------------
        $switcher = VanillaItems::SNOWBALL();
        $switcher->setCustomName("§r§bSwitcher");
        $switcher->setLore([
            "§a",
            "§7thorw this snowball to your enemy to change",
            "§7your position with him",
            "§f",
            "§ePurcharse at §3" . Loader::getInstance()->getConfig()->get('tebex')]);
        $switcher->getNamedTag()->setString("Abilities","Switcher");
        //------------------------------------------

        //------------------Strength----------------
        $strenght = VanillaItems::BLAZE_POWDER();
        $strenght->setCustomName("§r§cStrength II");
        $strenght->setLore([
            "§f",
            "§7this item will give you strength II",
            "§7for a few seconds",
            "§3",
            "§ePurcharse at §3" . Loader::getInstance()->getConfig()->get('tebex')]);
        $strenght->getNamedTag()->setString("Abilities","Strength");
        //------------------------------------------

        //------------------jumpboost----------------
        $jumpboost = VanillaItems::DYE()->setColor(DyeColor::PURPLE());
        $jumpboost->setCustomName("§r§6JumpBoost");
        $jumpboost->setLore([
            "§2",
            "§f§7this item will give you Jump Boost VII for",
            "§7a few seconds",
            "§f",
            "§ePurcharse at §3" . Loader::getInstance()->getConfig()->get('tebex')]);
        $jumpboost->getNamedTag()->setString("Abilities","JumpBoost");
        //------------------------------------------

        //------------------speed----------------
        $speed = VanillaItems::DYE()->setColor(DyeColor::LIME());
        $speed->setCustomName("§r§6Speed");
        $speed->setLore([
            "§2",
            "§7this item will give you Speed II for",
            "§7a few seconds",
            "§f",
            "§ePurcharse at §3" . Loader::getInstance()->getConfig()->get('tebex')]);
        $speed->getNamedTag()->setString("Abilities","Speed");
        //------------------------------------------

        //------------------resistance----------------
        $resistance = VanillaItems::IRON_INGOT();
        $resistance->setCustomName("§r§dResistance III");
        $resistance->setLore([
            "§f",
            "§7this item will give you Resistence III for",
            "§7a few seconds",
            "§f",
            "§ePurcharse at §3" . Loader::getInstance()->getConfig()->get('tebex')]);
        $resistance->getNamedTag()->setString("Abilities","Resistance");
        //------------------------------------------

        //------------------regeneration----------------
        $regeneration = VanillaItems::GOLD_INGOT();
        $regeneration->setCustomName("§r§6Regeneration");
        $regeneration->setLore([
            "§2",
            "§7this item will give you Regeneration III for",
            "§7a few seconds",
            "§f",
            "§ePurcharse at §3" . Loader::getInstance()->getConfig()->get('tebex')]);
        $regeneration->getNamedTag()->setString("Abilities","Regeneration");
        //------------------------------------------

        //------------------ballofrange----------------
        $ballofrange = VanillaItems::EGG();
        $ballofrange->setCustomName("§r§cBall of Range");
        $ballofrange->setLore([
            "§f",
            "§7when you throw this egg, the members of your",
            "§7faction that are near will receive strength II",
            "§7and Resistence III for a few seconds, and the",
            "§7enemies will receive Wither II",
            "§a",
            "§ePurchase at §3" . Loader::getInstance()->getConfig()->get('tebex')]);
        $ballofrange->getNamedTag()->setString("Abilities","BallOfRange");
        //------------------------------------------

        //------------------FireWork----------------
        $firework = self::getItem(401, 0, 1);
        $firework->setCustomName("§r§3Firework");
        $firework->setLore([
            "§f",
            "§7This is the item capable of rising like",
            "§7the firework so you can escape from enemies",
            "§a",
            "§ePurchase at §3" . Loader::getInstance()->getConfig()->get('tebex')]);
        $firework->getNamedTag()->setString("Abilities","Firework");
        //------------------------------------------

        //------------------Berserk----------------
        $berserk = VanillaItems::DYE()->setColor(DyeColor::RED());
        $berserk->setCustomName("§r§cBerserk");
        $berserk->setLore([
            "§f",
            "§7Use this item to get Strength I, Resistance II",
            "§7and Speed II for seconds",
            "§a",
            "§ePurchase at §3" . Loader::getInstance()->getConfig()->get('tebex')]);
        $berserk->getNamedTag()->setString("Abilities","Berserk");
        //------------------------------------------

        //------------------Ninja Star----------------
        $Ninjastar = VanillaItems::NETHER_STAR();
        $Ninjastar->setCustomName("§r§bNinja Star");
        $Ninjastar->setLore([
            "§f",
            "§7Teleports you to the last person who",
            "§7hit you in the last 15 seconds!",
            "§a",
            "§ePurchase at §3" . Loader::getInstance()->getConfig()->get('tebex')]);
        $Ninjastar->getNamedTag()->setString("Abilities","NinjaStar");
        //------------------------------------------

        //-----------------------------------------------------
        $Samurai = VanillaItems::DIAMOND_SWORD();
        $Samurai->setCustomName("§r§4Samurai");
        $Samurai->setLore([
            "§f",
            "§7Use this item to get Samurai Strength II",
            "§7and Speed II for seconds",
            "§a",
            "§ePurchase at §3" . Loader::getInstance()->getConfig()->get('tebex')]);
        $Samurai->getNamedTag()->setString("Abilities","Samurai");
        //---------------------------------------------

        //-----------------------------------------------------
        $FocusMode = VanillaItems::GOLD_NUGGET();
        $FocusMode->setCustomName("§gFocus Mode");
        $FocusMode->setLore([
            "§f",
            "§7Increases enemy damage by 30%",
            "§a",
            "§ePurchase at §3" . Loader::getInstance()->getConfig()->get('tebex')]);
        $FocusMode->getNamedTag()->setString("Abilities","FocusMode");
        //---------------------------------------------

        //-----------------------------------------------------
        $PocketBard = VanillaItems::DYE()->setColor(DyeColor::ORANGE());
        $PocketBard->setCustomName("§6Pocket Bard");
        $PocketBard->setLore([
            "§f",
            "§7Opens a menu for you to select bard effect abilities",
            "§a",
            "§ePurchase at §3" . Loader::getInstance()->getConfig()->get('tebex')]);
        $PocketBard->getNamedTag()->setString("Abilities","PocketBard");
        //---------------------------------------------

        //----------------------------------------
        $potion = VanillaItems::POTION();
        $potion->setCustomName("§4Potion Refill");
        $potion->setLore([
            "§f",
            "§7Fills your entire inventory of health potions 2",
            "§a",
            "§ePurchase at §3" . Loader::getInstance()->getConfig()->get('tebex')]);
        $potion->getNamedTag()->setString("Abilities","Potion");
        //----------------------------------------------

        //--------------------------------------------------
        $Risky_Mode = VanillaItems::IRON_NUGGET();
        $Risky_Mode->setCustomName("§bRicky Mode");
        $Risky_Mode->setLore([
            "§7",
            "§7Use this item to get Ricky Mode Strength II, Resistance 3",
            "§a",
            "§ePurchase at §3" . Loader::getInstance()->getConfig()->get('tebex')]);
        $Risky_Mode->getNamedTag()->setString("Abilities","RickyMode");
        //---------------------------------------------

        //----------------------------------------
        $strom = VanillaItems::STONE_AXE();
        $strom->setCustomName("§6Strom Breaker");
        $Risky_Mode->setLore([
            "§7",
            "§7Use this item to get Strom Breaker Strength II, Resistance 3",
            "§a",
            "§ePurchase at §3" . Loader::getInstance()->getConfig()->get('tebex')]);
        $Risky_Mode->getNamedTag()->setString("Abilities","Strom_Breaker");
        //--------------------------------------------------

        $combo = VanillaItems::CLOWNFISH();
        $combo->setCustomName("§6Combo Ability");
        $combo->setLore([
            "§7",
            "§7Use this item to getC Combo Ability Strength II, Resistance II",
            "§a",
            "§ePurchase at §3" . Loader::getInstance()->getConfig()->get('tebex')]);
        $combo->getNamedTag()->setString("Abilities","Combo");

        //------------------Ninja Star----------------
        $ReverseNinja = VanillaItems::NETHER_STAR();
        $ReverseNinja->setCustomName("§r§bReverse Ninja");
        $ReverseNinja->setLore([
            "§f",
            "§7Teleports you to the last person who",
            "§7hit you in the last 15 seconds!",
            "§a",
            "§ePurchase at §3" . Loader::getInstance()->getConfig()->get('tebex')]);
        $ReverseNinja->getNamedTag()->setString("Abilities","ReverseNinja");
        //------------------------------------------

        //------------------Graphin Hook----------------
        $GraphinHook = VanillaItems::FISHING_ROD();
        $GraphinHook->setCustomName("§r§9Graphin Hook");
        $GraphinHook->setLore([
            "§f",
            "§7It drives you in the direction you are going",
            "§7Cooldown:§c1:00",
            "§a",
            "§ePurchase at §3" . Loader::getInstance()->getConfig()->get('tebex')]);
        $GraphinHook->getNamedTag()->setString("Abilities","GraphinHook");
        //------------------------------------------

        //------------------Bard Effects----------------
        $effectsbard = VanillaItems::DYE()->setColor(DyeColor::ORANGE());
        $effectsbard->setCustomName("§r§bEffect Bard");
        $effectsbard->setLore([
            "§f",
            "§7Spawn entities is give effects the strength, resistance and speed",
            "§7Cooldown:§c1:00",
            "§a",
            "§ePurchase at §3" . Loader::getInstance()->getConfig()->get('tebex')]);
        $effectsbard->getNamedTag()->setString("Abilities","PortablePigs");
        //------------------------------------------
        
        //------------------Bard Effects----------------
        $portablerogue = VanillaItems::GOLDEN_SWORD();
        $portablerogue->setCustomName("§r§7PortableRogue");
        $portablerogue->setLore([
            "§f",
            "§7It gives the effects of rogué to the enemy ",
            "§7Cooldown:§c3:00",
            "§a",
            "§ePurchase at §3" . Loader::getInstance()->getConfig()->get('tebex')]);
        $portablerogue->getNamedTag()->setString("Abilities","PortableRogue");
        //------------------------------------------

        //------------------Thor----------------
        $thor = VanillaItems::IRON_AXE();
        $thor->setCustomName("§r§gThor");
        $thor->setLore([
            "§f",
            "§7Cae un rayo y te da efectos",
            "§7Cooldown:§c3:00",
            "§a",
            "§ePurchase at §3" . Loader::getInstance()->getConfig()->get('tebex')]);
        $thor->getNamedTag()->setString("Abilities","Thor");
        //------------------------------------------
        
        if ($sender instanceof Player) {
            $sender->getInventory()->addItem($ballofrange);
            $sender->getInventory()->addItem($abilitydisabler);
            $sender->getInventory()->addItem($exoticbone);
            $sender->getInventory()->addItem($firework);
            $sender->getInventory()->addItem($effectdisabler);
            $sender->getInventory()->addItem($portalebard);
            $sender->getInventory()->addItem($frezzergun);
            $sender->getInventory()->addItem($berserk);
            $sender->getInventory()->addItem($secondchance);
            $sender->getInventory()->addItem($switcher);
            $sender->getInventory()->addItem($jumpboost);
            $sender->getInventory()->addItem($regeneration);
            $sender->getInventory()->addItem($resistance);
            $sender->getInventory()->addItem($speed);
            $sender->getInventory()->addItem($strenght);
            $sender->getInventory()->addItem($Ninjastar);
            $sender->getInventory()->addItem($Samurai);
            $sender->getInventory()->addItem($FocusMode);
            $sender->getInventory()->addItem($potion);
            $sender->getInventory()->addItem($Risky_Mode);
            $sender->getInventory()->addItem($ReverseNinja);
            $sender->getInventory()->addItem($GraphinHook);
            $sender->getInventory()->addItem($effectsbard);
            $sender->getInventory()->addItem($portablerogue);
            $sender->getInventory()->addItem($thor);
        }

    }

    public static function getItem($id, $meta = 0, $count = 1): Item {
        return LegacyStringToItemParser::getInstance()->parse("{$id}:{$meta}")->setCount($count);
    }

}