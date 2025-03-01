<?php

declare(strict_types=1);

namespace hub\prefix\utils;

use hub\Loader;
use hub\player\Player;
use hub\prefix\Prefix;
use hub\utils\item\Items;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\block\VanillaBlocks;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\item\LegacyStringToItemParser;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\TextFormat;
use pocketmine\Server;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player as PlayerPlayer;

final class Utils
{
    
    /**
     * @param Player $player
     * @return CompoundTag
     */
    public static function createBasicNBT(Player $player): CompoundTag
    {
        $nbt = CompoundTag::create()
            ->setTag("Pos", new ListTag([
				new DoubleTag($player->getLocation()->x),
				new DoubleTag($player->getLocation()->y),
				new DoubleTag($player->getLocation()->z)
			]))
			->setTag("Motion", new ListTag([
				new DoubleTag($player->getMotion()->x),
				new DoubleTag($player->getMotion()->y),
				new DoubleTag($player->getMotion()->z)
			]))
			->setTag("Rotation", new ListTag([
				new FloatTag($player->getLocation()->yaw),
				new FloatTag($player->getLocation()->pitch)
			]));
        return $nbt;
    }

    /**
     * @param Player $player
     */

    public static function openPrefixMenu(Player $player): void {

        $menu = InvMenu::create(InvMenuTypeIds::TYPE_CHEST);
        $menu->getInventory()->setContents([
            0 => VanillaBlocks::REDSTONE_TORCH()->asItem()->setCustomName("§r§cReset Prefix")->setLore(["§eClick to reset your prefix..."]),
            11 => self::getItem(340, 0)->setCustomName("§r§l§cNormal"),
            12 => self::getItem(340, 0)->setCustomName("§r§l§cSpecials"),
            13 => self::getItem(340, 0)->setCustomName("§r§l§cSeason"),
            14 => self::getItem(340, 0)->setCustomName("§r§l§cEvents"),
            15 => self::getItem(340, 0)->setCustomName("§r§l§cCountry"),
        ]);

        $menu->setListener(function (InvMenuTransaction $transaction) use ($menu) : InvMenuTransactionResult {
            /** @var Player $player */
            $player = $transaction->getPlayer();
            $name = $player->getName();
            $item = $transaction->getItemClicked();
            $inventory = $menu->getInventory();

            if ($item->getCustomName() === ('§r§cReset Prefix')) {
                $player->getSession()->setPrefix(null);
                $player->sendMessage("§cyou have reset your prefix");
                $player->removeCurrentWindow();
            }

            if ($item->getCustomName() === ('§r§7Go Back')) {
                $inventory->clearAll();
                $inventory->setContents([

                    0 => VanillaBlocks::REDSTONE_TORCH()->asItem()->setCustomName("§r§cReset Prefix")->setLore(["§eClick to reset your prefix..."]),
                    11 => self::getItem(340, 0)->setCustomName("§r§l§cNormal"),
                    12 => self::getItem(340, 0)->setCustomName("§r§l§cSpecials"),
                    13 => self::getItem(340, 0)->setCustomName("§r§l§cSeason"),
                    14 => self::getItem(340, 0)->setCustomName("§r§l§cEvents"),
                    15 => self::getItem(340, 0)->setCustomName("§r§l§cCountry"),
                ]);
            }

            if ($item->getCustomName() === ('§r§l§cNormal')) {
                $inventory->clearAll();
                $inventory->setContents([
                    0 => self::getItem(262, 0)->setCustomName("§r§7Go Back"),
                    3 => self::getItem(339, 0)->setCustomName("§r§cReset Prefix")->setLore(["§eClick to reset your prefix..."]),
                    4 => self::getItem(138, 0)->setCustomName("§r§aPreview")->setLore(["", "§a{$name}: §fAbstract >> All MCPE Devs"]),
                    5 => self::getItem(339, 0)->setCustomName("§r§cReset Prefix")->setLore(["§eClick to reset your prefix..."]),
                    9 => self::getItem(337, 0)->setCustomName("§r§l§cCHEATER")->setLore(["§r§6[".self::getFormat('cheater')."§r§6] §a{$name}", "" , self::getText("cheater", $player)]),
                    10 => self::getItem(337, 0)->setCustomName("§r§l§cGOD")->setLore(["§r§6[".self::getFormat('god')."§r§6] §a{$name}", "" , self::getText("god", $player)]),
                    11 => self::getItem(337, 0)->setCustomName("§r§l§cHAX")->setLore(["§r§6[".self::getFormat('hax')."§r§6] §a{$name}", "" , self::getText("hax", $player)]),
                    12 => self::getItem(337, 0)->setCustomName("§r§l§cTOXIC")->setLore(["§r§6[".self::getFormat('toxic')."§r§6] §a{$name}", "" , self::getText("toxic", $player)]),
                    13 => self::getItem(337, 0)->setCustomName("§r§l§cNOOB")->setLore(["§r§6[".self::getFormat('noob')."§r§6] §a{$name}", "" , self::getText("noob", $player)]),
                    14 => self::getItem(337, 0)->setCustomName("§r§l§cHEART")->setLore(["§r§6[".self::getFormat('heart')."§r§6] §a{$name}", "" , self::getText("heart", $player)]),
                    15 => self::getItem(337, 0)->setCustomName("§r§l§cBOZO")->setLore(["§r§6[".self::getFormat('bozo')."§r§6] §a{$name}", "" , self::getText("bozo", $player)]),
                    16 => self::getItem(337, 0)->setCustomName("§r§l§cCRINGE")->setLore(["§r§6[".self::getFormat('cringe')."§r§6] §a{$name}", "" , self::getText("cringe", $player)]),
                    17 => self::getItem(337, 0)->setCustomName("§r§l§cLMAO")->setLore(["§r§6[".self::getFormat('lmao')."§r§6] §a{$name}", "" , self::getText("lmao", $player)]),
                    18 => self::getItem(337, 0)->setCustomName("§r§l§cWWW")->setLore(["§r§6[".self::getFormat('www')."§r§6] §a{$name}", "" , self::getText("www", $player)]),
                ]);
            }

            if ($item->getCustomName() === ('§r§l§cCHEATER')) {
                if ($player->hasPermission(self::getPermission("cheater"))) {
                    $player->getSession()->setPrefix("cheater");
                    $player->sendMessage(TextFormat::colorize("&ayou have placed the prefix " . self::getFormat("cheater")));
                    $player->removeCurrentWindow();
                } else {
                    $player->sendMessage(TextFormat::colorize("§cYou do not own this tag"));
                }
            }

            if ($item->getCustomName() === ('§r§l§cGOD')) {
                if ($player->hasPermission(self::getPermission("god"))) {
                    $player->getSession()->setPrefix("god");
                    $player->sendMessage(TextFormat::colorize("&ayou have placed the prefix " . self::getFormat("god")));
                    $player->removeCurrentWindow();
                } else {
                    $player->sendMessage(TextFormat::colorize("§cYou do not own this tag"));
                }
            }

            if ($item->getCustomName() === ('§r§l§cHAX')) {
                if ($player->hasPermission(self::getPermission("hax"))) {
                    $player->getSession()->setPrefix("hax");
                    $player->sendMessage(TextFormat::colorize("&ayou have placed the prefix " . self::getFormat("hax")));
                    $player->removeCurrentWindow();
                } else {
                    $player->sendMessage(TextFormat::colorize("§cYou do not own this tag"));
                }
            }

            if ($item->getCustomName() === ('§r§l§cTOXIC')) {
                if ($player->hasPermission(self::getPermission("toxic"))) {
                    $player->getSession()->setPrefix("toxic");
                    $player->sendMessage(TextFormat::colorize("&ayou have placed the prefix " . self::getFormat("toxic")));
                    $player->removeCurrentWindow();
                } else {
                    $player->sendMessage(TextFormat::colorize("§cYou do not own this tag"));
                }
            }

            if ($item->getCustomName() === ('§r§l§cNOOB')) {
                if ($player->hasPermission(self::getPermission("noob"))) {
                    $player->getSession()->setPrefix("noob");
                    $player->sendMessage(TextFormat::colorize("&ayou have placed the prefix " . self::getFormat("noob")));
                } else {
                    $player->sendMessage(TextFormat::colorize("§cYou do not own this tag"));
                }
            }

            if ($item->getCustomName() === ('§r§l§cHEART')) {
                if ($player->hasPermission(self::getPermission("heart"))) {
                    $player->getSession()->setPrefix("heart");
                    $player->sendMessage(TextFormat::colorize("&ayou have placed the prefix " . self::getFormat("heart")));
                    $player->removeCurrentWindow();
                } else {
                    $player->sendMessage(TextFormat::colorize("§cYou do not own this tag"));
                }
            }

            if ($item->getCustomName() === ('§r§l§cBOZO')) {
                if ($player->hasPermission(self::getPermission("bozo"))) {
                    $player->getSession()->setPrefix("bozo");
                    $player->sendMessage(TextFormat::colorize("&ayou have placed the prefix " . self::getFormat("bozo")));
                    $player->removeCurrentWindow();
                } else {
                    $player->sendMessage(TextFormat::colorize("§cYou do not own this tag"));
                }
            }
            

            if ($item->getCustomName() === ('§r§l§cCRINGE')) {
                if ($player->hasPermission(self::getPermission("cringe"))) {
                    $player->getSession()->setPrefix("cringe");
                    $player->sendMessage(TextFormat::colorize("&ayou have placed the prefix " . self::getFormat("cringe")));
                    $player->removeCurrentWindow();
                } else {
                    $player->sendMessage(TextFormat::colorize("§cYou do not own this tag"));
                }
            }

            if ($item->getCustomName() === ('§r§l§cLMAO')) {
                if ($player->hasPermission(self::getPermission("lmao"))) {
                    $player->getSession()->setPrefix("lmao");
                    $player->sendMessage(TextFormat::colorize("&ayou have placed the prefix " . self::getFormat("lmao")));
                    $player->removeCurrentWindow();
                } else {
                    $player->sendMessage(TextFormat::colorize("§cYou do not own this tag"));
                }
            }

            if ($item->getCustomName() === ('§r§l§cWWW')) {
                if ($player->hasPermission(self::getPermission("www"))) {
                    $player->getSession()->setPrefix("www");
                    $player->sendMessage(TextFormat::colorize("&ayou have placed the prefix " . self::getFormat("www")));
                    $player->removeCurrentWindow();
                } else {
                    $player->sendMessage(TextFormat::colorize("§cYou do not own this tag"));
                }
            }

            if ($item->getCustomName() === ('§r§l§cSpecials')) {
                $inventory->clearAll();
                $inventory->setContents([
                    0 => self::getItem(262, 0)->setCustomName("§r§7Go Back"),
                    3 => self::getItem(339, 0)->setCustomName("§r§cReset Prefix")->setLore(["§eClick to reset your prefix..."]),
                    4 => self::getItem(138, 0)->setCustomName("§r§aPreview")->setLore(["", "§a{$name}: §fAbstract >> All MCPE Devs"]),
                    5 => self::getItem(339, 0)->setCustomName("§r§cReset Prefix")->setLore(["§eClick to reset your prefix..."]),
                    9 => self::getItem(337, 0)->setCustomName("§r§l§cSIMP")->setLore(["§r§6[".self::getFormat('simp')."§r§6] §a{$name}", "" , self::getText("simp", $player)]),
                    10 => self::getItem(337, 0)->setCustomName("§r§l§cBANNED")->setLore(["§r§6[".self::getFormat('banned')."§r§6] §a{$name}", "" , self::getText("banned", $player)]),
                    11 => self::getItem(337, 0)->setCustomName("§r§l§cCLICKER")->setLore(["§r§6[".self::getFormat('clicker')."§r§6] §a{$name}", "" , self::getText("clicker", $player)]),
                    12 => self::getItem(337, 0)->setCustomName("§r§l§cMONO")->setLore(["§r§6[".self::getFormat('mono')."§r§6] §a{$name}", "" , self::getText("mono", $player)]),
                ]);
            }

            if ($item->getCustomName() === ('§r§l§cSIMP')) {
                if ($player->hasPermission(self::getPermission("simp"))) {
                    $player->getSession()->setPrefix("simp");
                    $player->sendMessage(TextFormat::colorize("&ayou have placed the prefix " . self::getFormat("simp")));
                    $player->removeCurrentWindow();
                } else {
                    $player->sendMessage(TextFormat::colorize("§cYou do not own this tag"));
                }
            }

            if ($item->getCustomName() === ('§r§l§cBANNED')) {
                if ($player->hasPermission(self::getPermission("banned"))) {
                    $player->getSession()->setPrefix("banned");
                    $player->sendMessage(TextFormat::colorize("&ayou have placed the prefix " . self::getFormat("banned")));
                    $player->removeCurrentWindow();
                } else {
                    $player->sendMessage(TextFormat::colorize("§cYou do not own this tag"));
                }
            }

            if ($item->getCustomName() === ('§r§l§cCLICKER')) {
                if ($player->hasPermission(self::getPermission("clicker"))) {
                    $player->getSession()->setPrefix("clicker");
                    $player->sendMessage(TextFormat::colorize("&ayou have placed the prefix " . self::getFormat("clicker")));
                    $player->removeCurrentWindow();
                } else {
                    $player->sendMessage(TextFormat::colorize("§cYou do not own this tag"));
                }
            }

            if ($item->getCustomName() === ('§r§l§cMONO')) {
                if ($player->hasPermission(self::getPermission("mono"))) {
                    $player->getSession()->setPrefix("mono");
                    $player->sendMessage(TextFormat::colorize("&ayou have placed the prefix " . self::getFormat("mono")));
                    $player->removeCurrentWindow();
                } else {
                    $player->sendMessage(TextFormat::colorize("§cYou do not own this tag"));
                }
            }

            if ($item->getCustomName() === ('§r§l§cSeason')) {
                $inventory->clearAll();
                $inventory->setContents([
                    0 => self::getItem(262, 0)->setCustomName("§r§7Go Back"),
                    3 => self::getItem(339, 0)->setCustomName("§r§cReset Prefix")->setLore(["§eClick to reset your prefix..."]),
                    4 => self::getItem(138, 0)->setCustomName("§r§aPreview")->setLore(["", "§a{$name}: §fAbstract >> All MCPE Devs"]),
                    5 => self::getItem(339, 0)->setCustomName("§r§cReset Prefix")->setLore(["§eClick to reset your prefix..."]),
                    9 => self::getItem(337, 0)->setCustomName("§r§l§cSUMMER")->setLore(["§r§6[".self::getFormat('summer')."§r§6] §a{$name}", "" , self::getText("summer", $player)]),
                    10 => self::getItem(337, 0)->setCustomName("§r§l§cCHRISTMAS")->setLore(["§r§6[".self::getFormat('christmas')."§r§6] §a{$name}", "" , self::getText("christmas", $player)]),
                    11 => self::getItem(337, 0)->setCustomName("§r§l§cANNIVERSARY")->setLore(["§r§6[".self::getFormat('anniversary')."§r§6] §a{$name}", "" , self::getText("anniversary", $player)]),
                    12 => self::getItem(337, 0)->setCustomName("§r§l§c2024")->setLore(["§r§6[".self::getFormat('2024')."§r§6] §a{$name}", "" , self::getText("2024", $player)]),
                ]);
            }

            if ($item->getCustomName() === ('§r§l§cSUMMER')) {
                if ($player->hasPermission(self::getPermission("summer"))) {
                    $player->getSession()->setPrefix("summer");
                    $player->sendMessage(TextFormat::colorize("&ayou have placed the prefix " . self::getFormat("summer")));
                    $player->removeCurrentWindow();
                } else {
                    $player->sendMessage(TextFormat::colorize("§cYou do not own this tag"));
                }
            }

            if ($item->getCustomName() === ('§r§l§cCHRISTMAS')) {
                if ($player->hasPermission(self::getPermission("christmas"))) {
                    $player->getSession()->setPrefix("christmas");
                    $player->sendMessage(TextFormat::colorize("&ayou have placed the prefix " . self::getFormat("christmas")));
                    $player->removeCurrentWindow();
                } else {
                    $player->sendMessage(TextFormat::colorize("§cYou do not own this tag"));
                }
            }

            if ($item->getCustomName() === ('§r§l§cANNIVERSARY')) {
                if ($player->hasPermission(self::getPermission("anniversary"))) {
                    $player->getSession()->setPrefix("anniversary");
                    $player->sendMessage(TextFormat::colorize("&ayou have placed the prefix " . self::getFormat("anniversary")));
                    $player->removeCurrentWindow();
                } else {
                    $player->sendMessage(TextFormat::colorize("§cYou do not own this tag"));
                }
            }

            if ($item->getCustomName() === ('§r§l§c2024')) {
                if ($player->hasPermission(self::getPermission("2024"))) {
                    $player->getSession()->setPrefix("2024");
                    $player->sendMessage(TextFormat::colorize("&ayou have placed the prefix " . self::getFormat("2024")));
                    $player->removeCurrentWindow();
                } else {
                    $player->sendMessage(TextFormat::colorize("§cYou do not own this tag"));
                }
            }

            if ($item->getCustomName() === ('§r§l§cEvents')) {
                $inventory->clearAll();
                $inventory->setContents([
                    0 => self::getItem(262, 0)->setCustomName("§r§7Go Back"),
                    3 => self::getItem(339, 0)->setCustomName("§r§cReset Prefix")->setLore(["§eClick to reset your prefix..."]),
                    4 => self::getItem(138, 0)->setCustomName("§r§aPreview")->setLore(["", "§a{$name}: §fAbstract >> All MCPE Devs"]),
                    5 => self::getItem(339, 0)->setCustomName("§r§cReset Prefix")->setLore(["§eClick to reset your prefix..."]),
                    9 => self::getItem(337, 0)->setCustomName("§r§l§cFTOP")->setLore(["§r§6[".self::getFormat('ftop')."§r§6] §a{$name}", "" , self::getText("ftop", $player)]),
                    10 => self::getItem(337, 0)->setCustomName("§r§l§cEOTW")->setLore(["§r§6[".self::getFormat('eotw')."§r§6] §a{$name}", "" , self::getText("eotw", $player)]),
                    11 => self::getItem(337, 0)->setCustomName("§r§l§cKILLER")->setLore(["§r§6[".self::getFormat('killer')."§r§6] §a{$name}", "" , self::getText("killer", $player)]),
                ]);
            }

            if ($item->getCustomName() === ('§r§l§cFTOP')) {
                if ($player->hasPermission(self::getPermission("ftop"))) {
                    $player->getSession()->setPrefix("ftop");
                    $player->sendMessage(TextFormat::colorize("&ayou have placed the prefix " . self::getFormat("ftop")));
                    $player->removeCurrentWindow();
                } else {
                    $player->sendMessage(TextFormat::colorize("§cYou do not own this tag"));
                }
            }

            if ($item->getCustomName() === ('§r§l§cEOTW')) {
                if ($player->hasPermission(self::getPermission("eotw"))) {
                    $player->getSession()->setPrefix("eotw");
                    $player->sendMessage(TextFormat::colorize("&ayou have placed the prefix " . self::getFormat("eotw")));
                    $player->removeCurrentWindow();
                } else {
                    $player->sendMessage(TextFormat::colorize("§cYou do not own this tag"));
                }
            }

            if ($item->getCustomName() === ('§r§l§cKILLER')) {
                if ($player->hasPermission(self::getPermission("killer"))) {
                    $player->getSession()->setPrefix("killer");
                    $player->sendMessage(TextFormat::colorize("&ayou have placed the prefix " . self::getFormat("killer")));
                    $player->removeCurrentWindow();
                } else {
                    $player->sendMessage(TextFormat::colorize("§cYou do not own this tag"));
                }
            }

            if ($item->getCustomName() === ('§r§l§cCountry')) {
                $inventory->clearAll();
                $inventory->setContents([
                    0 => self::getItem(262, 0)->setCustomName("§r§7Go Back"),
                    3 => self::getItem(339, 0)->setCustomName("§r§cReset Prefix")->setLore(["§eClick to reset your prefix..."]),
                    4 => self::getItem(138, 0)->setCustomName("§r§aPreview")->setLore(["", "§a{$name}: §fAbstract >> All MCPE Devs"]),
                    5 => self::getItem(339, 0)->setCustomName("§r§cReset Prefix")->setLore(["§eClick to reset your prefix..."]),
                    9 => self::getItem(337, 0)->setCustomName("§r§l§cMEX")->setLore(["§r§6[".self::getFormat('mex')."§r§6] §a{$name}", "" , self::getText("mex", $player)]),
                    10 => self::getItem(337, 0)->setCustomName("§r§l§cARG")->setLore(["§r§6[".self::getFormat('arg')."§r§6] §a{$name}", "" , self::getText("arg", $player)]),
                    11 => self::getItem(337, 0)->setCustomName("§r§l§cVNZ")->setLore(["§r§6[".self::getFormat('vnz')."§r§6] §a{$name}", "" , self::getText("vnz", $player)]),
                    12 => self::getItem(337, 0)->setCustomName("§r§l§cCHL")->setLore(["§r§6[".self::getFormat('chl')."§r§6] §a{$name}", "" , self::getText("chl", $player)]),
                    13 => self::getItem(337, 0)->setCustomName("§r§l§cBLV")->setLore(["§r§6[".self::getFormat('blv')."§r§6] §a{$name}", "" , self::getText("blv", $player)]),
                    14 => self::getItem(337, 0)->setCustomName("§r§l§cCOL")->setLore(["§r§6[".self::getFormat('col')."§r§6] §a{$name}", "" , self::getText("col", $player)]),
                    15 => self::getItem(337, 0)->setCustomName("§r§l§cECU")->setLore(["§r§6[".self::getFormat('ecu')."§r§6] §a{$name}", "" , self::getText("ecu", $player)]),
                    16 => self::getItem(337, 0)->setCustomName("§r§l§cPERU")->setLore(["§r§6[".self::getFormat('peru')."§r§6] §a{$name}", "" , self::getText("peru", $player)]),
                    17 => self::getItem(337, 0)->setCustomName("§r§l§cESP")->setLore(["§r§6[".self::getFormat('esp')."§r§6] §a{$name}", "" , self::getText("esp", $player)]),
                    18 => self::getItem(337, 0)->setCustomName("§r§l§cUSA")->setLore(["§r§6[".self::getFormat('usa')."§r§6] §a{$name}", "" , self::getText("usa", $player)]),
                ]);
            }

            if ($item->getCustomName() === ('§r§l§cMEX')) {
                if ($player->hasPermission(self::getPermission("mex"))) {
                    $player->getSession()->setPrefix("mex");
                    $player->sendMessage(TextFormat::colorize("&ayou have placed the prefix " . self::getFormat("mex")));
                    $player->removeCurrentWindow();
                } else {
                    $player->sendMessage(TextFormat::colorize("§cYou do not own this tag"));
                }
            }

            if ($item->getCustomName() === ('§r§l§cARG')) {
                if ($player->hasPermission(self::getPermission("arg"))) {
                    $player->getSession()->setPrefix("arg");
                    $player->sendMessage(TextFormat::colorize("&ayou have placed the prefix " . self::getFormat("arg")));
                    $player->removeCurrentWindow();
                } else {
                    $player->sendMessage(TextFormat::colorize("§cYou do not own this tag"));
                }
            }

            if ($item->getCustomName() === ('§r§l§cVNZ')) {
                if ($player->hasPermission(self::getPermission("vnz"))) {
                    $player->getSession()->setPrefix("vnz");
                    $player->sendMessage(TextFormat::colorize("&ayou have placed the prefix " . self::getFormat("vnz")));
                    $player->removeCurrentWindow();
                } else {
                    $player->sendMessage(TextFormat::colorize("§cYou do not own this tag"));
                }
            }

            if ($item->getCustomName() === ('§r§l§cCHL')) {
                if ($player->hasPermission(self::getPermission("chl"))) {
                    $player->getSession()->setPrefix("chl");
                    $player->sendMessage(TextFormat::colorize("&ayou have placed the prefix " . self::getFormat("chl")));
                    $player->removeCurrentWindow();
                } else {
                    $player->sendMessage(TextFormat::colorize("§cYou do not own this tag"));
                }
            }

            if ($item->getCustomName() === ('§r§l§cBLV')) {
                if ($player->hasPermission(self::getPermission("blv"))) {
                    $player->getSession()->setPrefix("blv");
                    $player->sendMessage(TextFormat::colorize("&ayou have placed the prefix " . self::getFormat("blv")));
                    $player->removeCurrentWindow();
                } else {
                    $player->sendMessage(TextFormat::colorize("§cYou do not own this tag"));
                }
            }

            if ($item->getCustomName() === ('§r§l§cCOL')) {
                if ($player->hasPermission(self::getPermission("col"))) {
                    $player->getSession()->setPrefix("col");
                    $player->sendMessage(TextFormat::colorize("&ayou have placed the prefix " . self::getFormat("col")));
                    $player->removeCurrentWindow();
                } else {
                    $player->sendMessage(TextFormat::colorize("§cYou do not own this tag"));
                }
            }

            if ($item->getCustomName() === ('§r§l§cECU')) {
                if ($player->hasPermission(self::getPermission("ecu"))) {
                    $player->getSession()->setPrefix("ecu");
                    $player->sendMessage(TextFormat::colorize("&ayou have placed the prefix " . self::getFormat("ecu")));
                    $player->removeCurrentWindow();
                } else {
                    $player->sendMessage(TextFormat::colorize("§cYou do not own this tag"));
                }
            }

            if ($item->getCustomName() === ('§r§l§cPERU')) {
                if ($player->hasPermission(self::getPermission("peru"))) {
                    $player->getSession()->setPrefix("peru");
                    $player->sendMessage(TextFormat::colorize("&ayou have placed the prefix " . self::getFormat("peru")));
                    $player->removeCurrentWindow();
                } else {
                    $player->sendMessage(TextFormat::colorize("§cYou do not own this tag"));
                }
            }

            if ($item->getCustomName() === ('§r§l§cESP')) {
                if ($player->hasPermission(self::getPermission("esp"))) {
                    $player->getSession()->setPrefix("esp");
                    $player->sendMessage(TextFormat::colorize("&ayou have placed the prefix " . self::getFormat("esp")));
                    $player->removeCurrentWindow();
                } else {
                    $player->sendMessage(TextFormat::colorize("§cYou do not own this tag"));
                }
            }

            if ($item->getCustomName() === ('§r§l§cUSA')) {
                if ($player->hasPermission(self::getPermission("usa"))) {
                    $player->getSession()->setPrefix("usa");
                    $player->sendMessage(TextFormat::colorize("&ayou have placed the prefix " . self::getFormat("usa")));
                    $player->removeCurrentWindow();
                } else {
                    $player->sendMessage(TextFormat::colorize("§cYou do not own this tag"));
                }
            }

            return $transaction->discard();
        });
        $menu->send($player, TextFormat::colorize('&r&3Prefixes &l&eNEW!'));

    }
    
    public static function getFormat(string $prefix): string {
        return TextFormat::colorize(Loader::getInstance()->getPrefixManager()->getPrefix($prefix)->getFormat());
    }

    public static function getPermission(string $prefix): string {
        return Loader::getInstance()->getPrefixManager()->getPrefix($prefix)->getPermission();
    }

    public static function getText(string $prefix, Player $player): string {
        
        if (!$player->hasPermission(Loader::getInstance()->getPrefixManager()->getPrefix($prefix)->getPermission())){
            return "§cYou do not own this tag";
        }
        return "§aYou can choose this tag";
    }

    public static function getItem($id, $meta = 0, $count = 1): Item {
        return LegacyStringToItemParser::getInstance()->parse("{$id}:{$meta}")->setCount($count);
    }
}
