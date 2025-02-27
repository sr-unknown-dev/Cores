<?php

declare(strict_types=1);

namespace hcf\module\coinshop\utils;

use hcf\Loader;
use hcf\player\Player;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\block\VanillaBlocks;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\item\LegacyStringToItemParser;
use pocketmine\item\VanillaItems;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\TextFormat;
use pocketmine\Server;
use pocketmine\console\ConsoleCommandSender;

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
    public static function openCoinShop(Player $player): void
    {
        $menu = InvMenu::create(InvMenuTypeIds::TYPE_DOUBLE_CHEST);
        $menu->getInventory()->setContents([
            0 => self::getItem(241, 1),
            1 => self::getItem(241, 1),
            7 => self::getItem(241, 1),
            8 => self::getItem(241, 1),
            9 => self::getItem(241, 1),
            17 => self::getItem(241, 1),
            36 => self::getItem(241, 1),
            44 => self::getItem(241, 1),
            45 => self::getItem(241, 1),
            46 => self::getItem(241, 1),
            52 => self::getItem(241, 1),
            53 => self::getItem(241, 1)
        ]);
        
        $menu->getInventory()->setItem(12, self::getItem(131)->setCustomName(TextFormat::colorize('&l&6Buy Keys')));
        $menu->getInventory()->setItem(13, self::getItem(311)->setCustomName(TextFormat::colorize('&l&bBuy GKits')));
        $menu->getInventory()->setItem(14, self::getItem(241)->setCustomName(TextFormat::colorize('&l&eBuy Temp Ranks')));
        $menu->getInventory()->setItem(23, self::getItem(403)->setCustomName(TextFormat::colorize('&r&l&5Menu Packages')));
        $menu->getInventory()->setItem(21, self::getItem(399)->setCustomName(TextFormat::colorize('&r&l&dMenu Events')));
        $menu->getInventory()->setItem(22, self::getItem(399)->setCustomName(TextFormat::colorize('&l&4Buy Exclusive Abilities')));
        
        $menu->setListener(function (InvMenuTransaction $transaction): InvMenuTransactionResult {
            $player = $transaction->getPlayer();
            $item = $transaction->getItemClicked();
            $type = TextFormat::clean($item->getCustomName());
            
            if (self::getPage($type) !== null) {
                self::openPageCoinShop($player, $type);
                $player->removeCurrentWindow();
            }
            return $transaction->discard();
        });
        $menu->send($player, TextFormat::colorize('&7Coin Shop, &gYour Coins: &e$'. $player->getSession()->getCrystals()));
    }
    
    /**
     * @param Player $player
     * @param string $type
     */
    public static function openPageCoinShop(Player $player, string $type): void
    {
        $menu = InvMenu::create(InvMenuTypeIds::TYPE_DOUBLE_CHEST);
        $menu->getInventory()->setContents([
            0 => self::getItem(241, 1),
            1 => self::getItem(241, 1),
            7 => self::getItem(241, 1),
            8 => self::getItem(241, 1),
            9 => self::getItem(241, 1),
            17 => self::getItem(241, 1),
            36 => self::getItem(241, 1),
            44 => self::getItem(241, 1),
            45 => self::getItem(241, 1),
            46 => self::getItem(241, 1),
            52 => self::getItem(241, 1),
            53 => self::getItem(241, 1)
        ] + self::getPage($type));
        
        $menu->setListener(function (InvMenuTransaction $transaction): InvMenuTransactionResult {
            /** @var Player $player */
            $player = $transaction->getPlayer();
            $item = $transaction->getItemClicked();
            
            if ($item->getNamedTag()->getTag('price') !== null) {
                $newItem = $item->setLore([]);
                $newBalance = $player->getSession()->getCrystals() - $item->getNamedTag()->getInt('price');

                if ($newBalance < 0) {
                    $player->sendMessage(TextFormat::colorize('&cYou do not have money to buy this item'));
                    return $transaction->discard();
                }
                
                if ($item->getCustomName() === TextFormat::colorize('&l&dKoth Key')) {
                    Server::getInstance()->dispatchCommand(new ConsoleCommandSender(Server::getInstance(), Server::getInstance()->getLanguage()), "key give Koth 2 ".'"'.$player->getName().'"');
                    $player->getSession()->setCrystals($newBalance);
                    $player->sendMessage(TextFormat::colorize("&aHas comprado &l&dKoth Key &r&aTu nuevo balance es &e$". $newBalance));
                    return $transaction->discard();
                }
                
                if ($item->getCustomName() === TextFormat::colorize('&l&eXeno Key')) {
                    Server::getInstance()->dispatchCommand(new ConsoleCommandSender(Server::getInstance(), Server::getInstance()->getLanguage()), "key give Xeno 3 ".'"'.$player->getName().'"');
                    $player->getSession()->setCrystals($newBalance);
                    $player->sendMessage(TextFormat::colorize("&aHas comprado &l&4Xeno Key &r&aTu nuevo balance es &e$". $newBalance));
                    return $transaction->discard();
                }
                
                if ($item->getCustomName() === TextFormat::colorize('&l&0BlackHole Key')) {
                    Server::getInstance()->dispatchCommand(new ConsoleCommandSender(Server::getInstance(), Server::getInstance()->getLanguage()), "key give Blackhole 2 ".'"'.$player->getName().'"');
                    $player->getSession()->setCrystals($newBalance);
                    $player->sendMessage(TextFormat::colorize("&aHas comprado &l&0BlackHole Key &r&aTu nuevo balance es &e$". $newBalance));
                    return $transaction->discard();
                }
                
                if ($item->getCustomName() === TextFormat::colorize('&l&6Solaris Key')) {
                    Server::getInstance()->dispatchCommand(new ConsoleCommandSender(Server::getInstance(), Server::getInstance()->getLanguage()), "key give Solaris 2 ".'"'.$player->getName().'"');
                    $player->getSession()->setCrystals($newBalance);
                    $player->sendMessage(TextFormat::colorize("&aHas comprado &l&6Supreme Key &r&aTu nuevo balance es &e$". $newBalance));
                    return $transaction->discard();
                }
                
                if ($item->getCustomName() === TextFormat::colorize('&l&5Partner Key')) {
                    Server::getInstance()->dispatchCommand(new ConsoleCommandSender(Server::getInstance(), Server::getInstance()->getLanguage()), "key give Partner 2 ".'"'.$player->getName().'"');
                    $player->getSession()->setCrystals($newBalance);
                    $player->sendMessage(TextFormat::colorize("&aHas comprado &l&5Athenas Key &r&aTu nuevo balance es &e$". $newBalance));
                    return $transaction->discard();
                }
                
                if ($item->getCustomName() === TextFormat::colorize('&l&7Starter Key')) {
                    Server::getInstance()->dispatchCommand(new ConsoleCommandSender(Server::getInstance(), Server::getInstance()->getLanguage()), "key give Starter 2 ".'"'.$player->getName().'"');
                    $player->getSession()->setCrystals($newBalance);
                    $player->sendMessage(TextFormat::colorize("&aHas comprado &l&7Starter Key &r&aTu nuevo balance es &e$". $newBalance));
                    return $transaction->discard();
                }
                
                if ($item->getCustomName() === TextFormat::colorize('&l&bBegginer Key')) {
                    Server::getInstance()->dispatchCommand(new ConsoleCommandSender(Server::getInstance(), Server::getInstance()->getLanguage()), "key give Begginer 2 ".'"'.$player->getName().'"');
                    $player->getSession()->setCrystals($newBalance);
                    $player->sendMessage(TextFormat::colorize("&aHas comprado &l&bBegginer Key &r&aTu nuevo balance es &e$". $newBalance));
                    return $transaction->discard();
                }
                
                if ($item->getCustomName() === TextFormat::colorize('&l&eAbilitys Key')) {
                    Server::getInstance()->dispatchCommand(new ConsoleCommandSender(Server::getInstance(), Server::getInstance()->getLanguage()), "key give Abilitys 2 ".'"'.$player->getName().'"');
                    $player->getSession()->setCrystals($newBalance);
                    $player->sendMessage(TextFormat::colorize("&aHas comprado &l&eAbilitys Key &r&aTu nuevo balance es &e$". $newBalance));
                    return $transaction->discard();
                }
                
                if ($item->getCustomName() === TextFormat::colorize('&l&5Pkg')) {
                    $senderName = $player->getName();
					Server::getInstance()->dispatchCommand(new ConsoleCommandSender(Server::getInstance(), Server::getInstance()->getLanguage()), 'pkg give "'. $senderName . '" 1');
                    $player->getSession()->setCrystals($newBalance);
                    $player->sendMessage(TextFormat::colorize("&aHaz comprado partnerpackages &e$". $newBalance));
                    return $transaction->discard();
                }
                
                if ($item->getCustomName() === TextFormat::colorize('&l&4Airdrop')) {
                    $senderName = $player->getName();
					Server::getInstance()->dispatchCommand(new ConsoleCommandSender(Server::getInstance(), Server::getInstance()->getLanguage()), 'airdrops give "'. $senderName . '" 1');
                    $player->getSession()->setCrystals($newBalance);
                    $player->sendMessage(TextFormat::colorize("&aHaz comprado airdrop &e$". $newBalance));
                    return $transaction->discard();
                }
                
                if ($item->getCustomName() === TextFormat::colorize('&l&2Mysterybox')) {
                    $senderName = $player->getName();
					Server::getInstance()->dispatchCommand(new ConsoleCommandSender(Server::getInstance(), Server::getInstance()->getLanguage()), 'mysterycrate give "'. $senderName . '" 1');
                    $player->getSession()->setCrystals($newBalance);
                    $player->sendMessage(TextFormat::colorize("&aHaz comprado mysterybox &e$". $newBalance));
                    return $transaction->discard();
                }
                
                if ($item->getCustomName() === TextFormat::colorize('&r&l&4Koth Event')) {
                    $senderName = $player->getName();
					Server::getInstance()->dispatchCommand(new ConsoleCommandSender(Server::getInstance(), Server::getInstance()->getLanguage()), "koth start Ruinas");
                    $player->getSession()->setCrystals($newBalance);
                    $player->sendMessage(TextFormat::colorize("&7Haz encendido una koth tu nuevo balance es de &a$". $newBalance));
                    return $transaction->discard();
                }
                
                if ($item->getCustomName() === TextFormat::colorize('&l&9Xeno &7GKit')) {
                    $senderName = $player->getName();
                    Server::getInstance()->dispatchCommand(new ConsoleCommandSender(Server::getInstance(), Server::getInstance()->getLanguage()), "voucher create ".'"'.$senderName.'"'." §l§9Xeno§7GKit kit give pay {player} Xeno");
                    $player->getSession()->setCrystals($newBalance);
                    $player->sendMessage(TextFormat::colorize("&aHas comprado &l&9Xeno &7GKit &r&aTu nuevo balance es &e$". $newBalance));
                    return $transaction->discard();
                }
                
                if ($item->getCustomName() === TextFormat::colorize('&l&6Solaris &7GKit')) {
                    $senderName = $player->getName();
					Server::getInstance()->dispatchCommand(new ConsoleCommandSender(Server::getInstance(), Server::getInstance()->getLanguage()), "voucher create ".'"'.$senderName.'"'." §l§6Solaris§7GKit kit give pay {player} Solaris");
                    $player->getSession()->setCrystals($newBalance);
                    $player->sendMessage(TextFormat::colorize("&aHas comprado &l&6Solaris &7GKit &r&aTu nuevo balance es &e$". $newBalance));
                    return $transaction->discard();
                }
                
                if ($item->getCustomName() === TextFormat::colorize('&l&bAstral &7GKit')) {
                    $senderName = $player->getName();
					Server::getInstance()->dispatchCommand(new ConsoleCommandSender(Server::getInstance(), Server::getInstance()->getLanguage()), "voucher create ".'"'.$senderName.'"'." §l§bAstral§7GKit kit give pay {player} Astral");
                    $player->getSession()->setCrystals($newBalance);
                    $player->sendMessage(TextFormat::colorize("&aHas comprado &l&bAstral &7GKit &r&aTu nuevo balance es &e$". $newBalance));
                    return $transaction->discard();
                }

                if ($item->getCustomName() === TextFormat::colorize('&l&4SullCrat &7Ability')) {
                    $sullcrat = VanillaItems::FIRE_CHARGE();
                    $sullcrat->setCustomName("§r§l§4SullCrat");
                    $sullcrat->setLore([
                        "§f",
                        "§7Use this item and after 5s",
                        "§7on hit block set air",
                        "§a",
                        "§ePurcharse at §3" . Loader::getInstance()->getConfig()->get('tebex')]);
                    $sullcrat->getNamedTag()->setString("Abilities","SullCrat");
                    $player->getInventory()->addItem($sullcrat);
                    $player->getSession()->setCrystals($newBalance);
                    $player->sendMessage(TextFormat::colorize("&aHas comprado &l&4SullCrat &7Ability &r&aTu nuevo balance es &e$". $newBalance));
                    return $transaction->discard();
                }
                
                if ($item->getCustomName() === TextFormat::colorize('&l&cOP&dRogue &7Gkit')) {
                    $senderName = $player->getName();
					Server::getInstance()->dispatchCommand(new ConsoleCommandSender(Server::getInstance(), Server::getInstance()->getLanguage()), "voucher create ".'"'.$senderName.'"'." §l§cOP§dRogue§7Gkit kit give pay {player} OpRogue");
                    $player->getSession()->setCrystals($newBalance);
                    $player->sendMessage(TextFormat::colorize("&aHas comprado &l&cOP&dRogue &7Gkit &r&aTu nuevo balance es &e$". $newBalance));
                    return $transaction->discard();
                }
                
                if ($item->getCustomName() === TextFormat::colorize('§r§9§lXeno§r §7Rank 5d')) {
                    $senderName = $player->getName();
					Server::getInstance()->dispatchCommand(new ConsoleCommandSender(Server::getInstance(), Server::getInstance()->getLanguage()), "ranks setrank ".'"'.$senderName.'"'." Xeno 5d");
                    $player->getSession()->setCrystals($newBalance);
                    $player->sendMessage(TextFormat::colorize("&aHas comprado &r§9§lXeno§&r &7Rank 5d &r&aTu nuevo balance es &e$". $newBalance));
                    return $transaction->discard();
                }
                
                if ($item->getCustomName() === TextFormat::colorize('§r§6§lSolaris§r §7Rank 5d')) {
                    $senderName = $player->getName();
					Server::getInstance()->dispatchCommand(new ConsoleCommandSender(Server::getInstance(), Server::getInstance()->getLanguage()), "ranks setrank ".'"'.$senderName.'"'." Solaris 5d");
                    $player->getSession()->setCrystals($newBalance);
                    $player->sendMessage(TextFormat::colorize("&aHas comprado &r§6§lSolaris&r &7Rank 5d &r&aTu nuevo balance es &e$". $newBalance));
                    return $transaction->discard();
                }
                
                if ($item->getCustomName() === TextFormat::colorize('§r§b§lAstral§r §7Rank 5d')) {
                    $senderName = $player->getName();
					Server::getInstance()->dispatchCommand(new ConsoleCommandSender(Server::getInstance(), Server::getInstance()->getLanguage()), "ranks setrank ".'"'.$senderName.'"'." Astral 5d");
                    $player->getSession()->setCrystals($newBalance);
                    $player->sendMessage(TextFormat::colorize("&aHas comprado &r§b§lAstral&r &7Rank 5d &r&aTu nuevo balance es &e$". $newBalance));
                    return $transaction->discard();
                }
                
                if ($item->getCustomName() === TextFormat::colorize('§r§c§lNova§r §7Rank 5d')) {
                    $senderName = $player->getName();
					Server::getInstance()->dispatchCommand(new ConsoleCommandSender(Server::getInstance(), Server::getInstance()->getLanguage()), "ranks setrank ".'"'.$senderName.'"'."  Nova 5d");
                    $player->getSession()->setCrystals($newBalance);
                    $player->sendMessage(TextFormat::colorize("&aHas comprado &r§c§lNova&r &7Rank 5d &r&aTu nuevo balance es &e$". $newBalance));
                    return $transaction->discard();
                }
                
                if ($item->getCustomName() === TextFormat::colorize('§r§d§lMoon§r §7Rank 5d')) {
                    $senderName = $player->getName();
					Server::getInstance()->dispatchCommand(new ConsoleCommandSender(Server::getInstance(), Server::getInstance()->getLanguage()), "ranks setrank ".'"'.$senderName.'"'." Moon 5d");
                    $player->getSession()->setCrystals($newBalance);
                    $player->sendMessage(TextFormat::colorize("&aHas comprado &r§d§lMoon&r &7Rank 5d &r&aTu nuevo balance es &e$". $newBalance));
                    return $transaction->discard();
                }
                
                if ($player->getInventory()->canAddItem($newItem)) {
                    $player->getInventory()->addItem($newItem);
                    $player->getSession()->setCrystals($newBalance);
                } else $player->sendMessage(TextFormat::colorize('&cThis item cannot be stored in your inventory'));
            }
            return $transaction->discard();
        });
        $menu->setInventoryCloseListener(function (Player $player, $inventory) {
            Loader::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($player): void {
                if ($player->isOnline())
                    self::openCoinShop($player);
            }), 1);
        });
        $menu->send($player, TextFormat::colorize('&7' . $type));
    }
    
    /**
     * @param Item $item
     * @param int $price
     * @return Item
     */
    private static function prepareItem(Item $item, int $price): Item
    {
        $item->setLore([TextFormat::colorize('&fPrice: &6$' . $price)]);
        
        $namedtag = $item->getNamedTag();
        $namedtag->setInt('price', $price);
        $item->setNamedTag($namedtag);
        
        return $item;
    }
    
    /**
     * @param string $type
     * @return array|null
     */
    private static function getPage(string $type): ?array
    {
        switch ($type) {
            case 'Buy Keys':
                return [
                    13 => self::prepareItem(self::getItem(351, 1, 1), 50)->setCustomName(TextFormat::colorize('&l&dKoth Key')),
                    21 => self::prepareItem(self::getItem(351, 7, 1), 50)->setCustomName(TextFormat::colorize('&l&4Xeno Key')),
                    22 => self::prepareItem(self::getItem(351, 14, 1), 50)->setCustomName(TextFormat::colorize('&l&0Blackhole Key')),
                    23 => self::prepareItem(self::getItem(351, 11, 1), 10)->setCustomName(TextFormat::colorize('&l&6Solaris Key')),
                    30 => self::prepareItem(self::getItem(372, 1, 1), 30)->setCustomName(TextFormat::colorize('&l&5Partner Key')),
                    31 => self::prepareItem(self::getItem(351, 10, 1), 3)->setCustomName(TextFormat::colorize('&l&7Starter Key')),
                    32 => self::prepareItem(self::getItem(351, 5, 1), 10)->setCustomName(TextFormat::colorize('&l&eBegginer')),
                    40 => self::prepareItem(self::getItem(351, 12, 1), 30)->setCustomName(TextFormat::colorize('&l&bAbilitys Key'))
                ];
                
            case 'Buy GKits':
                return [
                    22 => self::prepareItem(self::getItem(399, 1, 1), 20)->setCustomName(TextFormat::colorize('&l&bBluddy &7GKit')),
                    30 => self::prepareItem(self::getItem(299, 1, 1), 10)->setCustomName(TextFormat::colorize('&l&eMoon &7GKit')),
                    31 => self::prepareItem(self::getItem(315, 1, 1), 10)->setCustomName(TextFormat::colorize('&l&6Nova &7GKit')),
                    32 => self::prepareItem(self::getItem(303, 12, 1), 10)->setCustomName(TextFormat::colorize('&l&cOP&dRogue &7Gkit'))
                ];
                
            case 'Buy Temp Ranks':
                return [
                    21 => self::prepareItem(self::getItem(241, 9, 1), 50)->setCustomName(TextFormat::colorize('§r§b§lMoon§r §7Rank 5d')),
                    22 => self::prepareItem(self::getItem(241, 4, 1), 100)->setCustomName(TextFormat::colorize('§r§e§lNova§r §7Rank 5d')),
                    23 => self::prepareItem(self::getItem(241, 1, 1), 150)->setCustomName(TextFormat::colorize('§r§5§lAstral§r §7Rank 5d')),
                    30 => self::prepareItem(self::getItem(241, 12, 1), 200)->setCustomName(TextFormat::colorize('§r§6§lSolaris§r §7Rank 5d')),
                    31 => self::prepareItem(self::getItem(241, 8, 1), 250)->setCustomName(TextFormat::colorize('§r§c§lXeno§r §7Rank 5d')),
                    
                    ];

            case 'Buy Exclusive Abilities':
                return [
                    21 => self::prepareItem(self::getItem(385), 100)->setCustomName(TextFormat::colorize('&l&4SullCrat &7Ability')),
                ];
                
                 case 'Menu Packages':
                return [
                    21 => self::prepareItem(self::getItem(241, 9, 1), 5)->setCustomName(TextFormat::colorize('&l&5Pkg')),
                    22 => self::prepareItem(self::getItem(241, 4, 1), 10)->setCustomName(TextFormat::colorize('&l&4Airdrop')),
                    30 => self::prepareItem(self::getItem(241, 12, 1), 15)->setCustomName(TextFormat::colorize('&l&2Mysterybox')),
                
                ];
                
                 case 'Menu Events':
                return [
                    21 => self::prepareItem(self::getItem(264, 0, 1), 15)->setCustomName(TextFormat::colorize('&r&l&4Koth Event')),
                    
                ];
        }
        return null;
    }

    public static function getItem($id, $meta = 0, $count = 1): Item {
        return LegacyStringToItemParser::getInstance()->parse("{$id}:{$meta}")->setCount($count);
    }
}
