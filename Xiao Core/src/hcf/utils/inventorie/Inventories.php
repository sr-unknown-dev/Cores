<?php

declare(strict_types=1);

namespace hcf\utils\inventorie;

use CortexPE\DiscordWebhookAPI\Embed;
use CortexPE\DiscordWebhookAPI\Message;
use CortexPE\DiscordWebhookAPI\Webhook;
use hcf\abilities\items\NinjaStar;
use hcf\command\moderador\EventsCommand;
use hcf\entity\AbilityEntity;
use hcf\handler\kit\classes\presets\Archer;
use hcf\Loader;
use hcf\kits\ArcherOp;
use hcf\kits\BardOp;
use hcf\kits\Extreme;
use hcf\kits\Leviathan;
use hcf\kits\RogueOp;
use hcf\kits\Supreme;
use hcf\kits\ghostly;
use hcf\kits\ghostlyPlus;
use hcf\koth\command\subcommand\StartSubCommand;
use hcf\player\Player;
use hcf\timer\types\TimerAirdrop;
use hcf\timer\types\TimerDeath;
use hcf\timer\types\TimerFFA;
use hcf\timer\types\TimerKey;
use hcf\timer\types\TimerKeyOP;
use hcf\timer\types\TimerLoobox;
use hcf\timer\types\TimerMystery;
use hcf\timer\types\TimerPackages;
use hcf\timer\types\TimerPurge;
use hcf\timer\types\TimerSotw;
use hcf\timer\types\Timerx2Points;
use hcf\utils\coldowns\Cooldowns;
use hcf\utils\item\Items;
use hcf\utils\time\Timer;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\utils\MobHeadType;
use pocketmine\block\VanillaBlocks;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\inventory\Inventory;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\Item;
use pocketmine\item\LegacyStringToItemParser;
use pocketmine\item\VanillaItems;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as TE;
use pocketmine\utils\TextFormat;
use xoapp\staffmode\items\Vanish;

/**
 * Class Inventories
 * @package hcf\utils
 */
final class Inventories
{

    public static array $players = [];

    public static function BountyMenu(Player $player): void {
        $menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
        $inventory = $menu->getInventory();

        $bounties = Loader::getInstance()->getBountyManager()->getAllBountys();
        $slot = 0;

        foreach($bounties as $target => $data) {
            $paper = VanillaItems::PAPER();
            $paper->setCustomName(TextFormat::colorize("&r&l&6" . $target));

            $paper->setLore([
                TextFormat::colorize("&r&7▶ &aBounty Amount: &f$" . $data["Amount"]),
                TextFormat::colorize("&r&7▶ &aSet by: &f" . $data["Player"]),
                "",
                TextFormat::colorize("&r&7Click to track this bounty")
            ]);

            $inventory->setItem($slot++, $paper);
        }

        $menu->setListener(function (InvMenuTransaction $transaction) use ($bounties): InvMenuTransactionResult {
            $item = $transaction->getItemClicked();
            $player = $transaction->getPlayer();

            $clickedName = TextFormat::clean($item->getCustomName());

            if(isset($bounties[$clickedName])) {
                $targetPlayer = Loader::getInstance()->getServer()->getPlayerByPrefix($clickedName);
                if($targetPlayer instanceof Player) {
                    Loader::getInstance()->getBountyManager()->trackBounty($player, $targetPlayer);
                    $player->sendMessage(TextFormat::colorize("&aBounty is activated"));
                } else {
                    $player->sendMessage(TextFormat::colorize("&cTarget player is offline"));
                }
            }

            return $transaction->discard();
        });

        $menu->send($player, TextFormat::colorize("&l&aBounties"));
    }

    public static function createCrateContent(Player $player, array $data): void
    {
        $menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
        $menu->setInventoryCloseListener(function (Player $player, Inventory $inventory) use ($data): void {
            $data['items'] = $inventory->getContents();
            Loader::getInstance()->getHandlerManager()->getCrateManager()->addCrate($data['crateName'], $data['key'], $data['keyFormat'], $data['color'], $data['nameFormat'], (array) $data['items']);

            $chest = VanillaBlocks::SHULKER_BOX()->asItem();
            $chest->setCustomName(TE::colorize('Crate ' . $data['crateName']));
            $namedtag = $chest->getNamedTag();
            $namedtag->setString('crate_place', $data['crateName']);
            $chest->setNamedTag($namedtag);

            $player->getInventory()->addItem($chest);
            $player->sendMessage(TE::colorize('&aYou have successfully created the crate ' . $data['crateName']));
        });
        $menu->send($player, TE::colorize('&4Crate content'));
    }

    public static function editCrateContent(Player $player, string $crateName): void
    {
        $crate = Loader::getInstance()->getHandlerManager()->getCrateManager()->getCrate($crateName);

        $menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
        $menu->getInventory()->setContents($crate?->getItems());
        $menu->setInventoryCloseListener(function (Player $player, Inventory $inventory) use ($crate): void {
            $crate?->setItems($inventory->getContents());
            $player->sendMessage(TE::colorize('&aYou have edited the content'));
        });
        $menu->send($player, TE::colorize('&4Edit crate'));
    }

    public static function SelectEvents(Player $player, $time): void
    {
        $menu = InvMenu::create(InvMenu::TYPE_CHEST);
        $hcf = VanillaItems::PAPER();
        $hcf->setCustomName(TE::colorize("&gHCF &fEvents"));
        $map = VanillaItems::PAPER();
        $map->setCustomName(TE::colorize("&gMap &fEvents"));
        for ($i = 0; $i < 54; $i++) {
            $glass = VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::YELLOW())->asItem()->setCustomName(" ");
            if ($menu->getInventory()->getItem($i) == VanillaItems::AIR()) {
                $menu->getInventory()->setItem($i, $glass);
            }
        }

        $menu->getInventory()->setItem(11, $hcf);
        $menu->getInventory()->setItem(15, $map);
        $menu->setListener(function (InvMenuTransaction $transaction) use ($time): InvMenuTransactionResult {
            /** @var Player */
            $player = $transaction->getPlayer();
            $item = $transaction->getItemClicked()->getCustomName();

            if ($item === TE::colorize("&gHCF &fEvents")) {
                $this->HCFEvents($player, $time);
            }

            if ($item === TE::colorize("&gMap &fEvents")) {
                $this->MapEvents($player, $time);
            }
            return $transaction->discard();
        });
        $menu->send($player, TE::colorize('&9Events select Menu'));
    }

    public static function HCFEvents(Player $player, $time): void
    {
        $menu = InvMenu::create(InvMenu::TYPE_CHEST);
        $keyall = VanillaItems::DYE()->setColor(DyeColor::RED());
        $keyall->setCustomName(TE::colorize("&aKeyall"));
        $opkeyall = VanillaItems::ENCHANTED_GOLDEN_APPLE();
        $opkeyall->setCustomName(TE::colorize("&9OpKeyall"));
        $airdropall = VanillaItems::GOLD_NUGGET();
        $airdropall->setCustomName(TE::colorize("&3Airdropall"));
        $pkgall = VanillaBlocks::ENDER_CHEST()->asItem();
        $pkgall->setCustomName(TE::colorize("&5Pkgall"));
        $lootbox = VanillaBlocks::BEACON()->asItem();
        $lootbox->setCustomName(TE::colorize("&9Lootboxall"));
        $mystery = VanillaBlocks::DYED_SHULKER_BOX()->setColor(DyeColor::BLUE())->asItem();
        $mystery->setCustomName(TE::colorize("&4Mysteryall"));

        $menu->getInventory()->setItem(4, $keyall);
        $menu->getInventory()->setItem(12, $opkeyall);
        $menu->getInventory()->setItem(13, $airdropall);
        $menu->getInventory()->setItem(14, $pkgall);
        $menu->getInventory()->setItem(11, $lootbox);
        $menu->getInventory()->setItem(15, $mystery);
        $menu->setListener(function (InvMenuTransaction $transaction) use ($time): InvMenuTransactionResult {
            /** @var Player */
            $player = $transaction->getPlayer();
            $item = $transaction->getItemClicked()->getCustomName();


            if ($item === TE::colorize("&aKeyall")) {
                $player->sendMessage(TE::colorize("&aKeyall has starter"));
                Loader::getInstance()->getServer()->broadcastMessage(TE::colorize("&7███████"));
                Loader::getInstance()->getServer()->broadcastMessage(TE::colorize("&7█&a█&7███&a█&7█"));
                Loader::getInstance()->getServer()->broadcastMessage(TE::colorize("&7█&a█&7██&a█&7██ &r&7[&g&lghostly &l&aKEYALL&r&7]"));
                Loader::getInstance()->getServer()->broadcastMessage(TE::colorize("&7█&a███&7███ &r&aKeyall &ghas starter for: &f" . Timer::Format($time)));
                Loader::getInstance()->getServer()->broadcastMessage(TE::colorize("&7█&a█&7██&a█&7██ "));
                Loader::getInstance()->getServer()->broadcastMessage(TE::colorize("&7█&a█&7███&a█&7█"));
                Loader::getInstance()->getServer()->broadcastMessage(TE::colorize("&7█&a█&7███&a█&7█"));
                Loader::getInstance()->getServer()->broadcastMessage(TE::colorize("&7███████"));
                $webHook = new Webhook(Loader::getInstance()->getConfig()->get('hcf.webhook'));
                $msg = new Message();
                $msg->setContent('<@&1184267398501629962>');
                $embed = new Embed();
                $embed->setTitle("KeyAll has started");
                $embed->setColor(0xf9ff1a);
                $embed->setDescription("⏳Time: " . Timer::Format($time) . "\nIp: Ghostly.ddns.net\nPort: 19120\nStore: https://ghostly.tebex.io/");

                $embed->setFooter("Ghostly Network");
                $msg->addEmbed($embed);

                $webHook->send($msg);
                TimerKey::start($time);
            }

            if ($item === TE::colorize("&9OpKeyall")) {
                $player->sendMessage(TE::colorize("&9OpKeyall &ahas starter"));
                Loader::getInstance()->getServer()->broadcastMessage(TE::colorize("&7███████"));
                Loader::getInstance()->getServer()->broadcastMessage(TE::colorize("&7█&3█&7███&3█&7█"));
                Loader::getInstance()->getServer()->broadcastMessage(TE::colorize("&7█&3█&7██&3█&7██ &r&7[&g&lghostly &l&3OPKYEALL&r&7]"));
                Loader::getInstance()->getServer()->broadcastMessage(TE::colorize("&7█&3███&7███ &r&3OpKeyall &ghas starter for: &f" . Timer::Format($time)));
                Loader::getInstance()->getServer()->broadcastMessage(TE::colorize("&7█&3█&7██&3█&7██ "));
                Loader::getInstance()->getServer()->broadcastMessage(TE::colorize("&7█&3█&7███&3█&7█"));
                Loader::getInstance()->getServer()->broadcastMessage(TE::colorize("&7█&3█&7███&3█&7█"));
                Loader::getInstance()->getServer()->broadcastMessage(TE::colorize("&7███████"));
                Loader::getInstance()->getServer()->broadcastMessage(TE::colorize("&7███████"));
                $webHook = new Webhook(Loader::getInstance()->getConfig()->get('hcf.webhook'));
                $msg = new Message();
                $msg->setContent('<@&1184267398501629962>');
                $embed = new Embed();
                $embed->setTitle("OpKeyAll has started");
                $embed->setColor(0xf9ff1a);
                $embed->setDescription("⏳Time: " . Timer::Format($time) . "\nIp: Ghostly.ddns.net\nPort: 19120\nStore: https://ghostly.tebex.io/");

                $embed->setFooter("Ghostly Network");
                $msg->addEmbed($embed);

                $webHook->send($msg);
                TimerKeyOP::start($time);
            }

            if ($item === TE::colorize("&3Airdropall")) {
                $player->sendMessage(TE::colorize("&3Airdropall &ahas starter"));
                $webHook = new Webhook(Loader::getInstance()->getConfig()->get('hcf.webhook'));
                $msg = new Message();
                $msg->setContent('<@&1184267398501629962>');
                $embed = new Embed();
                $embed->setTitle("AirdropAll has started");
                $embed->setColor(0xf9ff1a);
                $embed->setDescription("⏳Time: " . Timer::Format($time) . "\nIp: Ghostly.ddns.net\nPort: 19120\nStore: https://ghostly.tebex.io/");

                $embed->setFooter("Ghostly Network");
                $msg->addEmbed($embed);

                $webHook->send($msg);
                TimerAirdrop::start($time);
            }

            if ($item === TE::colorize("&5Pkgall")) {
                $player->sendMessage(TE::colorize("&5Pkgall &ahas starter"));
                Loader::getInstance()->getServer()->broadcastMessage(TE::colorize("&7███████"));
                Loader::getInstance()->getServer()->broadcastMessage(TE::colorize("&7█&5█&5███&7█&7█"));
                Loader::getInstance()->getServer()->broadcastMessage(TE::colorize("&7█&5█&7██&5█&7██ &r&7[&g&lghostly &l&5PPALL&r&7]"));
                Loader::getInstance()->getServer()->broadcastMessage(TE::colorize("&7█&5█&5██&5█&7██ &r&5Ppall &ghas starter for: &f" . Timer::Format($time)));
                Loader::getInstance()->getServer()->broadcastMessage(TE::colorize("&7█&5█&7███&7█&7█"));
                Loader::getInstance()->getServer()->broadcastMessage(TE::colorize("&7█&5█&7███&7█&7█"));
                Loader::getInstance()->getServer()->broadcastMessage(TE::colorize("&7███████"));
                $webHook = new Webhook(Loader::getInstance()->getConfig()->get('hcf.webhook'));
                $msg = new Message();
                $msg->setContent('<@&1184267398501629962>');
                $embed = new Embed();
                $embed->setTitle("PkgAll has started");
                $embed->setColor(0xf9ff1a);
                $embed->setDescription("⏳Time: " . Timer::Format($time) . "\nIp: Ghostly.ddns.net\nPort: 19120\nStore: https://ghostly.tebex.io/");

                $embed->setFooter("Ghostly Network");
                $msg->addEmbed($embed);

                $webHook->send($msg);
                TimerPackages::start($time);
            }

            if ($item === TE::colorize("&4Mysteryall")) {
                $player->sendMessage(TE::colorize("&4Mysteryall &ahas starter"));
                $webHook = new Webhook(Loader::getInstance()->getConfig()->get('hcf.webhook'));
                $msg = new Message();
                $msg->setContent('<@&1184267398501629962>');
                $embed = new Embed();
                $embed->setTitle("MysteryAll has started");
                $embed->setColor(0xf9ff1a);
                $embed->setDescription("⏳Time: " . Timer::Format($time) . "\nIp: Ghostly.ddns.net\nPort: 19120\nStore: https://ghostly.tebex.io/");

                $embed->setFooter("Ghostly Network");
                $msg->addEmbed($embed);

                $webHook->send($msg);
                TimerMystery::start($time);
            }

            if ($item === TE::colorize("&cLootboxall")) {
                $player->sendMessage(TE::colorize("&cLootboxall &ahas starter"));
                $webHook = new Webhook(Loader::getInstance()->getConfig()->get('hcf.webhook'));
                $msg = new Message();
                $msg->setContent('<@&1184267398501629962>');
                $embed = new Embed();
                $embed->setTitle("LootboxAll has started");
                $embed->setColor(0xf9ff1a);
                $embed->setDescription("⏳Time: " . Timer::Format($time) . "\nIp: Ghostly.ddns.net\nPort: 19120\nStore: https://ghostly.tebex.io/");

                $embed->setFooter("Ghostly Network");
                $msg->addEmbed($embed);

                $webHook->send($msg);
                TimerLoobox::start($time);
            }
            return $transaction->discard();
        });
        $menu->send($player, TE::colorize('&9HCF Events Menu'));
    }

    public static function MapEvents(Player $player, $time): void
    {
        $menu = InvMenu::create(InvMenu::TYPE_CHEST);
        $sotw = VanillaItems::ENCHANTED_GOLDEN_APPLE();
        $sotw->setCustomName(TE::colorize("&aSotw"));
        $eotw = VanillaItems::GOLD_NUGGET();
        $eotw->setCustomName(TE::colorize("&4Eotw"));
        $purge = VanillaBlocks::ENDER_CHEST()->asItem();
        $purge->setCustomName(TE::colorize("&4Purge"));
        $death = VanillaBlocks::BEACON()->asItem();
        $death->setCustomName(TE::colorize("&bDeath"));
        $x2points = VanillaBlocks::DYED_SHULKER_BOX()->setColor(DyeColor::BLUE())->asItem();
        $x2points->setCustomName(TE::colorize("&3x2 Points"));
        $ffa = VanillaItems::DIAMOND_SWORD();
        $ffa->setCustomName(TE::colorize("&aFFA"));

        $menu->getInventory()->setItem(4, $sotw);
        $menu->getInventory()->setItem(11, $eotw);
        $menu->getInventory()->setItem(12, $purge);
        $menu->getInventory()->setItem(13, $death);
        $menu->getInventory()->setItem(14, $x2points);
        $menu->getInventory()->setItem(15, $ffa);
        $menu->setListener(function (InvMenuTransaction $transaction) use ($time): InvMenuTransactionResult {
            /** @var Player */
            $player = $transaction->getPlayer();
            $item = $transaction->getItemClicked()->getCustomName();


            if ($item === TE::colorize("&aSotw")) {
                $player->sendMessage(TE::colorize("&aSotw has starter"));
                Loader::getInstance()->getTimerManager()->getSotw()->setActive(true);
                Loader::getInstance()->getTimerManager()->getSotw()->setTime((int) $time);
            }

            if ($item === TE::colorize("&4Eotw")) {
                $player->sendMessage(TE::colorize("&4Eotw &ahas starter"));
                Loader::getInstance()->getServer()->broadcastMessage(TE::colorize("&7███████"));
                Loader::getInstance()->getServer()->broadcastMessage(TE::colorize("&7█&4█████&7█"));
                Loader::getInstance()->getServer()->broadcastMessage(TE::colorize("&7█&4█&7██&7█&7██ &r&7[&g&lghostly &l&4EOTW&r&7]"));
                Loader::getInstance()->getServer()->broadcastMessage(TE::colorize("&7█&4█████&7█ &r&4EOTW &ghas starter for: &f" . Timer::Format($time)));
                Loader::getInstance()->getServer()->broadcastMessage(TE::colorize("&7█&4█&7██&7█&7██ "));
                Loader::getInstance()->getServer()->broadcastMessage(TE::colorize("&7█&4█&7███&7█&7█"));
                Loader::getInstance()->getServer()->broadcastMessage(TE::colorize("&7█&4█████&7█"));
                Loader::getInstance()->getServer()->broadcastMessage(TE::colorize("&7███████"));
                $webHook = new Webhook(Loader::getInstance()->getConfig()->get('hcf.webhook'));
                $msg = new Message();
                $msg->setContent('<@&1184267398501629962>');
                $embed = new Embed();
                $embed->setTitle("Eotw Start");
                $embed->setColor(0xf9ff1a);
                $embed->setDescription("⏳ Duration: " . Timer::Format($time) . "\n⏳ Purge Durarion: 20m\nIp: \nStore: https://ghostly.tebex.io/");

                $embed->setFooter("HCF");
                $msg->addEmbed($embed);


                $webHook->send($msg);
                Loader::getInstance()->getTimerManager()->getEotw()->setActive(true);
                Loader::getInstance()->getTimerManager()->getEotw()->setTime((int) $time);
            }

            if ($item === TE::colorize("&4Purge")) {
                $player->sendMessage(TE::colorize("&4Purge &ahas starter"));
                Loader::getInstance()->getServer()->broadcastMessage(TE::colorize("&7███████"));
                Loader::getInstance()->getServer()->broadcastMessage(TE::colorize("&7█&4█&4███&7█&7█"));
                Loader::getInstance()->getServer()->broadcastMessage(TE::colorize("&7█&4█&7██&4█&7██ &r&7[&g&lghostly &l&4PURGE&r&7]"));
                Loader::getInstance()->getServer()->broadcastMessage(TE::colorize("&7█&4█&4██&4█&7██ &r&4Purge &ghas starter for: &f" . Timer::Format($time)));
                Loader::getInstance()->getServer()->broadcastMessage(TE::colorize("&7█&4█&7███&7█&7█"));
                Loader::getInstance()->getServer()->broadcastMessage(TE::colorize("&7█&4█&7███&7█&7█"));
                Loader::getInstance()->getServer()->broadcastMessage(TE::colorize("&7███████"));
                $webHook = new Webhook(Loader::getInstance()->getConfig()->get('hcf.webhook'));
                $msg = new Message();
                $msg->setContent('<@&1184267398501629962>');
                $embed = new Embed();
                $embed->setTitle("Purge Start");
                $embed->setColor(0xf9ff1a);
                $embed->setDescription("⏳ Duration: " . Timer::Format($time) . "\n\nIp: Ghostly.ddns.net\nPort: 19120\nStore: https://ghostly.tebex.io/");

                $embed->setFooter("HCF");
                $msg->addEmbed($embed);
                Loader::getInstance()->getTimerManager()->getPurge()->setActive(true);
                Loader::getInstance()->getTimerManager()->getPurge()->setTime((int) $time);
            }

            if ($item === TE::colorize("&bDeath")) {
                $player->sendMessage(TE::colorize("&bDeath &ahas starter"));
                $webHook = new Webhook(Loader::getInstance()->getConfig()->get('hcf.webhook'));
                $msg = new Message();
                $msg->setContent('<@&1212177469231337484>');
                $embed = new Embed();
                $embed->setTitle("Death has started");
                $embed->setColor(0xf9ff1a);
                $embed->setDescription("⏳Time: " . Timer::Format($time) . "\nIp: Ghostly.ddns.net\nPort: 19120\nStore: https://ghostly.tebex.io/");

                $embed->setFooter("Ghostly Network");
                $msg->addEmbed($embed);

                $webHook->send($msg);
                Loader::getInstance()->getTimerManager()->getDeath()->setActive(true);
                Loader::getInstance()->getTimerManager()->getDeath()->setTime((int) $time);
            }

            if ($item === TE::colorize("&3x2 Points")) {
                $player->sendMessage(TE::colorize("&3x2 Points &ahas starter"));
                $webHook = new Webhook(Loader::getInstance()->getConfig()->get('hcf.webhook'));
                $msg = new Message();
                $msg->setContent('<@&1184267398501629962>');
                $embed = new Embed();
                $embed->setTitle("X2Points has started");
                $embed->setColor(0xf9ff1a);
                $embed->setDescription("⏳Time: " . Timer::Format($time) . "\n\nIp: Ghostly.ddns.net\nPort: 19120\nStore: https://ghostly.tebex.io/");

                $embed->setFooter("Ghostly Network");
                $msg->addEmbed($embed);

                $webHook->send($msg);
                Loader::getInstance()->getTimerManager()->getPoints()->setActive(true);
                Loader::getInstance()->getTimerManager()->getPoints()->setTime((int) $time);
            }

            if ($item === TE::colorize("&aFFA")) {
                $player->sendMessage("§aFFA has been started");
                Loader::getInstance()->getServer()->broadcastMessage(TE::colorize("&7███████"));
                Loader::getInstance()->getServer()->broadcastMessage(TE::colorize("&7█&a█&a███&a█&7█"));
                Loader::getInstance()->getServer()->broadcastMessage(TE::colorize("&7█&a█&7██&7█&7██ &r&7[&g&lghostly &l&aFFA&r&7]"));
                Loader::getInstance()->getServer()->broadcastMessage(TE::colorize("&7█&a███&a█&7██ &r&aFFA &ghas starter for: &f" . Timer::Format($time)));
                Loader::getInstance()->getServer()->broadcastMessage(TE::colorize("&7█&a█&7██&7█&7██ "));
                Loader::getInstance()->getServer()->broadcastMessage(TE::colorize("&7█&a█&7███&7█&7█"));
                Loader::getInstance()->getServer()->broadcastMessage(TE::colorize("&7█&a█&7███&7█&7█"));
                Loader::getInstance()->getServer()->broadcastMessage(TE::colorize("&7███████"));
                $webHook = new Webhook(Loader::getInstance()->getConfig()->get('hcf.webhook'));
                $msg = new Message();
                $msg->setContent('<@&1184267398501629962>');
                $embed = new Embed();
                $embed->setTitle("FFA has started");
                $embed->setColor(0xf9ff1a);
                $embed->setDescription("⏳Time: " . Timer::Format($time) . "\nIp: Ghostly.ddns.net\nPort: 19120\nStore: https://ghostly.tebex.io/");

                $embed->setFooter("Ghostly Network");
                $msg->addEmbed($embed);

                $webHook->send($msg);
                TimerFFA::start($time);
            }
            return $transaction->discard();
        });
        $menu->send($player, TE::colorize('&9Map Events Menu'));
    }

    public static function Menu(Player $player)
    {
        $menu = InvMenu::create(InvMenu::TYPE_CHEST);
        $sword = VanillaItems::DIAMOND_SWORD();
        $sword->setCustomName("Message");
        $menu->getInventory()->setItem(13, $sword);
        $menu->setListener(function (InvMenuTransaction $transaction): InvMenuTransactionResult {
            $player = $transaction->getPlayer();
            $item = $transaction->getItemClicked()->getCustomName();

            if ($item === "Message") {
                $player->sendMessage("Moviste la espada");
            }
            return $transaction->discard();
        });

        $menu->send($player, TextFormat::colorize("&l&bMenu"));
    }

    /**
     * @param Player $player
     * @param $link
     * @return void
     */
    public static function LiveMenu(Player $player, $link): void
    {
        self::$players[$player->getName()]["Nuevo Video"] = false;
        self::$players[$player->getName()]["En Vivo"] = false;
        $menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
        foreach (self::ItemsLives() as $slot => $item) {
            $menu->getInventory()->setItem($slot, $item);
        }
        $menu->setListener(listener: function (InvMenuTransaction $transaction) use ($menu, $link): InvMenuTransactionResult {
            $item = $transaction->getItemClicked();

            if ($item->getCustomName() === "§aNuevo video") {
                $menu->getInventory()->setItem(11, $item->setCustomName("§bNuevo video"));
                self::$players[$transaction->getPlayer()->getName()]["Nuevo Video"] = true;
                return $transaction->discard();
            } elseif ($item->getCustomName() === "§bNuevo video") {
                $menu->getInventory()->setItem(11, $item->setCustomName("§aNuevo video"));
                self::$players[$transaction->getPlayer()->getName()]["Nuevo Video"] = false;
                return $transaction->discard();
            }
            if ($item->getCustomName() === "§4En Vivo") {
                $menu->getInventory()->setItem(15, $item->setCustomName("§bEn Vivo"));
                self::$players[$transaction->getPlayer()->getName()]["En Vivo"] = true;
                return $transaction->discard();
            } elseif ($item->getCustomName() === "§bEn Vivo") {
                $menu->getInventory()->setItem(15, $item->setCustomName("§4En Vivo"));
                self::$players[$transaction->getPlayer()->getName()]["En Vivo"] = false;
                return $transaction->discard();
            }
            if ($item->getCustomName() === "§6Send Message") {
                $transaction->getPlayer()->removeCurrentWindow();
                Loader::getInstance()->getServer()->broadcastMessage(TE::colorize("&g" . $transaction->getPlayer()->getName() . "&8 | &aA iniciado un &r\n-" . self::getClass($transaction->getPlayer()) . "\n&gLink: &f" . $link));
                self::$players[$transaction->getPlayer()->getName()]["Nuevo Video"] = false;
                self::$players[$transaction->getPlayer()->getName()]["En Vivo"] = false;
                return $transaction->discard();
            }
            return $transaction->discard();
        });
        $menu->send($player, "§gLives Menu");
    }

    /**
     * @return array
     */
    public static function ItemsLives(): array
    {
        $items = [
            11 => VanillaItems::DYE()->setColor(DyeColor::GREEN())->setCustomName("§aNuevo video"),
            15 => VanillaItems::DYE()->setColor(DyeColor::RED())->setCustomName("§4En Vivo"),
            31 => VanillaItems::DYE()->setColor(DyeColor::ORANGE())->setCustomName("§6Send Message"),
        ];
        return $items;
    }

    /**
     * @param Player $player
     * @return string
     */
    public static function getClass(Player $player): string
    {
        $class = [];
        if (self::$players[$player->getName()]["Nuevo Video"] === true) {
            $class[] = "§aNuevo video";
        }
        if (self::$players[$player->getName()]["En Vivo"] === true) {
            $class[] = "§4En Vivo";
        }
        return implode("\n-", $class);
    }

    public static function PortableKits(Player $player): void
    {
        $menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
        $menu->getInventory()->setItem(4, new ghostlyPlus);
        $menu->getInventory()->setItem(11, new ghostly);
        $menu->getInventory()->setItem(12, new Leviathan);
        $menu->getInventory()->setItem(13, new Supreme);
        $menu->getInventory()->setItem(14, new Extreme);
        $menu->getInventory()->setItem(15, new ArcherOp);
        $menu->getInventory()->setItem(22, new RogueOp);
        $menu->getInventory()->setItem(31, new BardOp);
        $menu->setListener(function (InvMenuTransaction $transaction): InvMenuTransactionResult {
            $player = $transaction->getPlayer();
            $transaction->getPlayer()->removeCurrentWindow();
            return $transaction->discard();
        });
        $menu->send($player, "§9PortableKits Menu");
    }

    /**
     * @param Player $player
     */
    public static function createKitOrganization(Player $player): void
    {
        $menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
        $organization = Loader::getInstance()->getHandlerManager()->getKitManager()->getOrganization();
        for ($i = 0; $i < 54; $i++) {
            if (isset($organization[$i])) {
                $kit = Loader::getInstance()->getHandlerManager()->getKitManager()->getKit($organization[$i]);

                if ($kit !== null)
                    $menu->getInventory()->setItem($i, Items::createItemKitOrganization($player, $kit->getRepresentativeItem(), $kit->getName()));
                else $menu->getInventory()->setItem($i, VanillaBlocks::AIR()->asItem());
            } else
                $menu->getInventory()->setItem($i, VanillaBlocks::AIR()->asItem());
        }
        $menu->setListener(function (InvMenuTransaction $transaction): InvMenuTransactionResult {
            /** @var Player */
            $player = $transaction->getPlayer();
            $item = $transaction->getItemClicked();

            if ($item->getNamedTag()->getTag('kit_name') !== null) {
                $kit = Loader::getInstance()->getHandlerManager()->getKitManager()->getKit($item->getNamedTag()->getString('kit_name'));

                if ($kit !== null) {
                    if (Loader::getInstance()->getTimerManager()->getFreeKits()->isEnable()) {
                        if ($player->getSession()->getCooldown('kit.' . $kit->getName()) !== null) {
                            $player->sendMessage(TE::colorize('&cYou have kit cooldown. Time remaining ' . Timer::convert($player->getSession()->getCooldown('kit.' . $kit->getName())->getTime())));
                            return $transaction->discard();
                        }
    
                        $kit->giveTo($player);
                        
                        if ($kit->getCooldown() !== 0)
                            $player->getSession()->addCooldown('kit.' . $kit->getName(), '', $kit->getCooldown(), false, false);
                    }else {
                        
                        if ($kit->getPermission() !== null && !$player->hasPermission($kit->getPermission())) {
                            $player->sendMessage(TE::colorize('&cYou do not have permission to use the kit'));
                            return $transaction->discard();
                        }
                        
                        # Cooldown
                        if ($player->getSession()->getCooldown('kit.' . $kit->getName()) !== null) {
                            $player->sendMessage(TE::colorize('&cYou have kit cooldown. Time remaining ' . Timer::convert($player->getSession()->getCooldown('kit.' . $kit->getName())->getTime())));
                            return $transaction->discard();
                        }
    
                        # Give kit
                        $kit->giveTo($player);
                        
                        # Add cooldown
                        if ($kit->getCooldown() !== 0)
                            $player->getSession()->addCooldown('kit.' . $kit->getName(), '', $kit->getCooldown(), false, false);
                    }
                }
            }
            return $transaction->discard();
        });
        $menu->send($player, TE::colorize('&eKits Free'));
    }

    public static function createMenuKit(Player $player)
    {
        $menu = InvMenu::create(InvMenu::TYPE_CHEST);

        $menu->getInventory()->setItem(11, VanillaItems::IRON_SWORD()->setCustomName(TE::colorize("&gFree &bKits")));
        $menu->getInventory()->setItem(13, VanillaItems::NETHERITE_SWORD()->setCustomName(TE::colorize("&gOp &bKits")));
        $menu->getInventory()->setItem(15, VanillaItems::DIAMOND_SWORD()->setCustomName(TE::colorize("&gPay &bKits")));
        $menu->setListener(function (InvMenuTransaction $transaction): InvMenuTransactionResult {
            $player = $transaction->getPlayer();
            $item = $transaction->getItemClicked();

            if ($item->getCustomName() === TE::colorize("&gFree &bKits")) {
                self::createKitOrganization($player);
                return $transaction->discard();
            }
            if ($item->getCustomName() === TE::colorize("&gPay &bKits")) {
                self::createKitPayOrganization($player);
                return $transaction->discard();
            }

            if ($item->getCustomName() === TE::colorize("&gOp &bKits")) {
                self::createKitOpOrganization($player);
                return $transaction->discard();
            }
            return $transaction->discard();
        });
        $menu->send($player, TE::colorize('&eKits Menu'));
    }

    public static function createKitPayOrganization(Player $player): void
    {
        $menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
        $organization = Loader::getInstance()->getHandlerManager()->getKitPayManager()->getOrganization();
        for ($i = 0; $i < 54; $i++) {
            if (isset($organization[$i])) {
                $kit = Loader::getInstance()->getHandlerManager()->getKitPayManager()->getKit($organization[$i]);

                if ($kit !== null)
                    $menu->getInventory()->setItem($i, Items::createItemKitPayOrganization($player, $kit->getRepresentativeItem(), $kit->getName()));
                else $menu->getInventory()->setItem($i, VanillaBlocks::AIR()->asItem());
            } else
                $menu->getInventory()->setItem($i, VanillaBlocks::AIR()->asItem());
        }
        $menu->setListener(function (InvMenuTransaction $transaction): InvMenuTransactionResult {
            /** @var Player */
            $player = $transaction->getPlayer();
            $item = $transaction->getItemClicked();

            if ($item->getNamedTag()->getTag('kit_name') !== null) {
                $kit = Loader::getInstance()->getHandlerManager()->getKitPayManager()->getKit($item->getNamedTag()->getString('kit_name'));

                if ($kit !== null) {
                    if (Loader::getInstance()->getTimerManager()->getFreeKits()->isEnable()) {
                        if ($player->getSession()->getCooldown('kit.' . $kit->getName()) !== null) {
                            $player->sendMessage(TE::colorize('&cYou have kit cooldown. Time remaining ' . Timer::convert($player->getSession()->getCooldown('kit.' . $kit->getName())->getTime())));
                            return $transaction->discard();
                        }
    
                        $kit->giveTo($player);
                        
                        if ($kit->getCooldown() !== 0)
                            $player->getSession()->addCooldown('kit.' . $kit->getName(), '', $kit->getCooldown(), false, false);
                    }else {
                        
                        if ($kit->getPermission() !== null && !$player->hasPermission($kit->getPermission())) {
                            $player->sendMessage(TE::colorize('&cYou do not have permission to use the kit'));
                            return $transaction->discard();
                        }
                        
                        # Cooldown
                        if ($player->getSession()->getCooldown('kit.' . $kit->getName()) !== null) {
                            $player->sendMessage(TE::colorize('&cYou have kit cooldown. Time remaining ' . Timer::convert($player->getSession()->getCooldown('kit.' . $kit->getName())->getTime())));
                            return $transaction->discard();
                        }
    
                        # Give kit
                        $kit->giveTo($player);
                        
                        # Add cooldown
                        if ($kit->getCooldown() !== 0)
                            $player->getSession()->addCooldown('kit.' . $kit->getName(), '', $kit->getCooldown(), false, false);
                    }
                }
            }
            return $transaction->discard();
        });
        $menu->send($player, TE::colorize('&eKits Pay'));
    }

    public static function createKitOpOrganization(Player $player): void
    {
        $menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
        $organization = Loader::getInstance()->getHandlerManager()->getKitOpManager()->getOrganization();
        for ($i = 0; $i < 54; $i++) {
            if (isset($organization[$i])) {
                $kit = Loader::getInstance()->getHandlerManager()->getKitOpManager()->getKit($organization[$i]);

                if ($kit !== null)
                    $menu->getInventory()->setItem($i, Items::createItemKitOpOrganization($player, $kit->getRepresentativeItem(), $kit->getName()));
                else $menu->getInventory()->setItem($i, VanillaBlocks::AIR()->asItem());
            } else
                $menu->getInventory()->setItem($i, VanillaBlocks::AIR()->asItem());
        }
        $menu->setListener(function (InvMenuTransaction $transaction): InvMenuTransactionResult {
            /** @var Player */
            $player = $transaction->getPlayer();
            $item = $transaction->getItemClicked();

            if ($item->getNamedTag()->getTag('kit_name') !== null) {
                $kit = Loader::getInstance()->getHandlerManager()->getKitOpManager()->getKit($item->getNamedTag()->getString('kit_name'));

                if ($kit !== null) {
                    if (Loader::getInstance()->getTimerManager()->getFreeKits()->isEnable()) {
                        if ($player->getSession()->getCooldown('kit.' . $kit->getName()) !== null) {
                            $player->sendMessage(TE::colorize('&cYou have kit cooldown. Time remaining ' . Timer::convert($player->getSession()->getCooldown('kit.' . $kit->getName())->getTime())));
                            return $transaction->discard();
                        }
    
                        $kit->giveTo($player);
                        
                        if ($kit->getCooldown() !== 0)
                            $player->getSession()->addCooldown('kit.' . $kit->getName(), '', $kit->getCooldown(), false, false);
                    }else {
                        
                        if ($kit->getPermission() !== null && !$player->hasPermission($kit->getPermission())) {
                            $player->sendMessage(TE::colorize('&cYou do not have permission to use the kit'));
                            return $transaction->discard();
                        }
                        
                        # Cooldown
                        if ($player->getSession()->getCooldown('kit.' . $kit->getName()) !== null) {
                            $player->sendMessage(TE::colorize('&cYou have kit cooldown. Time remaining ' . Timer::convert($player->getSession()->getCooldown('kit.' . $kit->getName())->getTime())));
                            return $transaction->discard();
                        }
    
                        # Give kit
                        $kit->giveTo($player);
                        
                        # Add cooldown
                        if ($kit->getCooldown() !== 0)
                            $player->getSession()->addCooldown('kit.' . $kit->getName(), '', $kit->getCooldown(), false, false);
                    }
                }
            }
            return $transaction->discard();
        });
        $menu->send($player, TE::colorize('&eKits Op'));
    }

    public static function editKitOrganization(Player $player): void
    {
        $menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);

        foreach (Loader::getInstance()->getHandlerManager()->getKitManager()->getOrganization() as $slot => $kitName) {
            $kit = Loader::getInstance()->getHandlerManager()->getKitManager()->getKit($kitName);

            if ($kit !== null) $menu->getInventory()->setItem($slot, Items::createItemKitOrganization($player, $kit->getRepresentativeItem(), $kit->getName()));
        }
        $menu->setListener(function (InvMenuTransaction $transaction): InvMenuTransactionResult {
            /** @var Player */
            $player = $transaction->getPlayer();
            $item = $transaction->getItemClickedWith();

            if (!$item->isNull() && $item->getNamedTag()->getTag('kit_name') === null)
                return $transaction->discard();
            return $transaction->continue();
        });
        $menu->setInventoryCloseListener(function (Player $player, Inventory $inventory): void {
            $data = [];
            $contents = $inventory->getContents();

            foreach ($contents as $slot => $item) {
                $kit = Loader::getInstance()->getHandlerManager()->getKitManager()->getKit($item->getNamedTag()->getString('kit_name'));

                if ($kit !== null) $data[$slot] = $kit->getName();
            }
            Loader::getInstance()->getHandlerManager()->getKitManager()->setOrganization($data);
        });
        $menu->send($player, TE::colorize('&6Edit kit organization'));
    }

    public static function editKitPayOrganization(Player $player): void
    {
        $menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);

        foreach (Loader::getInstance()->getHandlerManager()->getKitPayManager()->getOrganization() as $slot => $kitName) {
            $kit = Loader::getInstance()->getHandlerManager()->getKitPayManager()->getKit($kitName);

            if ($kit !== null) $menu->getInventory()->setItem($slot, Items::createItemKitPayOrganization($player, $kit->getRepresentativeItem(), $kit->getName()));
        }
        $menu->setListener(function (InvMenuTransaction $transaction): InvMenuTransactionResult {
            /** @var Player */
            $player = $transaction->getPlayer();
            $item = $transaction->getItemClickedWith();

            if (!$item->isNull() && $item->getNamedTag()->getTag('kit_name') === null)
                return $transaction->discard();
            return $transaction->continue();
        });
        $menu->setInventoryCloseListener(function (Player $player, Inventory $inventory): void {
            $data = [];
            $contents = $inventory->getContents();

            foreach ($contents as $slot => $item) {
                $kit = Loader::getInstance()->getHandlerManager()->getKitPayManager()->getKit($item->getNamedTag()->getString('kit_name'));

                if ($kit !== null) $data[$slot] = $kit->getName();
            }
            Loader::getInstance()->getHandlerManager()->getKitPayManager()->setOrganization($data);
        });
        $menu->send($player, TE::colorize('&6Edit kit organization'));
    }

    public static function editKitOpOrganization(Player $player): void
    {
        $menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);

        foreach (Loader::getInstance()->getHandlerManager()->getKitOpManager()->getOrganization() as $slot => $kitName) {
            $kit = Loader::getInstance()->getHandlerManager()->getKitOpManager()->getKit($kitName);

            if ($kit !== null) $menu->getInventory()->setItem($slot, Items::createItemKitOpOrganization($player, $kit->getRepresentativeItem(), $kit->getName()));
        }
        $menu->setListener(function (InvMenuTransaction $transaction): InvMenuTransactionResult {
            /** @var Player */
            $player = $transaction->getPlayer();
            $item = $transaction->getItemClickedWith();

            if (!$item->isNull() && $item->getNamedTag()->getTag('kit_name') === null)
                return $transaction->discard();
            return $transaction->continue();
        });
        $menu->setInventoryCloseListener(function (Player $player, Inventory $inventory): void {
            $data = [];
            $contents = $inventory->getContents();

            foreach ($contents as $slot => $item) {
                $kit = Loader::getInstance()->getHandlerManager()->getKitOpManager()->getKit($item->getNamedTag()->getString('kit_name'));

                if ($kit !== null) $data[$slot] = $kit->getName();
            }
            Loader::getInstance()->getHandlerManager()->getKitOpManager()->setOrganization($data);
        });
        $menu->send($player, TE::colorize('&6Edit kit organization'));
    }

    public static function Abilitys(Player $player): void
    {

        $menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
        $menu->setName("§l§bAbilitys");

        //------------------TimeWarp----------------
        $timewarp = VanillaItems::STONE_AXE();
        $timewarp->setCustomName("§r§aTime Warp");
        $timewarp->setLore([
            "§f",
            "§7Use this item and after 5s",
            "§7it will take you the last position where you pearled within 15s",
            "§a",
            "§ePurcharse at §3" . Loader::getInstance()->getConfig()->get('tebex')
        ]);
        $timewarp->getNamedTag()->setString("Abilities", "TimeWarp");
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
            "§ePurcharse at §3" . Loader::getInstance()->getConfig()->get('tebex')
        ]);
        $exoticbone->getNamedTag()->setString("Abilities", "ExoticBone");
        //------------------------------------------

        //------------------EffectDisabler----------------
        $effectdisabler = VanillaItems::SLIMEBALL();
        $effectdisabler->setCustomName("§r§aEffects Disabler");
        $effectdisabler->setLore([
            "",
            "§7hit a player with the item",
            "§7to clear the effects of the other player!",
            "§f",
            "§ePurcharse at §3" . Loader::getInstance()->getConfig()->get('tebex')
        ]);
        $effectdisabler->getNamedTag()->setString("Abilities", "EffectDisabler");
        //------------------------------------------

        //------------------PortableBard----------------
        $portalebard = VanillaItems::ZOMBIE_SPAWN_EGG()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::INFINITY(), 1));
        $portalebard->setCustomName("§r§dPortable Bard");
        $portalebard->setLore([
            "§f",
            "§7Using the spawn egg will create a witch and they hold negative effects to your enemies!",
            "§a",
            "§ePurcharse at §3" . Loader::getInstance()->getConfig()->get('tebex')
        ]);
        $portalebard->getNamedTag()->setString("Abilities", "PortableBard");
        //------------------------------------------

        //------------------FreezerGun----------------
        $frezzergun = VanillaItems::SNOWBALL();
        $frezzergun->setCustomName("§r§4Freezer Gun");
        $frezzergun->setLore([
            "§f",
            "§7When shooting an enemy, they will be completely frozen for a certain time",
            "§a",
            "§ePurcharse at §3" . Loader::getInstance()->getConfig()->get('tebex')
        ]);
        $frezzergun->getNamedTag()->setString("Abilities", "FrezzerGun");
        //------------------------------------------

        //------------------SecondChance----------------
        $secondchance = VanillaItems::GHAST_TEAR();
        $secondchance->setCustomName("§r§bSecondChance");
        $secondchance->setLore([
            "§f",
            "§7This item will remove the enderpearl cooldown",
            "§7to give you a second chance",
            "§2",
            "§ePurcharse at §3" . Loader::getInstance()->getConfig()->get('tebex')
        ]);
        $secondchance->getNamedTag()->setString("Abilities", "SecondChance");
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
            "§ePurcharse at §3" . Loader::getInstance()->getConfig()->get('tebex')
        ]);
        $abilitydisabler->getNamedTag()->setString("Abilities", "AbilityDisabler");
        //------------------------------------------

        //------------------Switcher----------------
        $switcher = VanillaItems::SNOWBALL();
        $switcher->setCustomName("§r§bSwitcher");
        $switcher->setLore([
            "§a",
            "§7thorw this snowball to your enemy to change",
            "§7your position with him",
            "§f",
            "§ePurcharse at §3" . Loader::getInstance()->getConfig()->get('tebex')
        ]);
        $switcher->getNamedTag()->setString("Abilities", "Switcher");
        //------------------------------------------

        //------------------Strength----------------
        $strenght = VanillaItems::BLAZE_POWDER();
        $strenght->setCustomName("§r§cStrength II");
        $strenght->setLore([
            "§f",
            "§7this item will give you strength II",
            "§7for a few seconds",
            "§3",
            "§ePurcharse at §3" . Loader::getInstance()->getConfig()->get('tebex')
        ]);
        $strenght->getNamedTag()->setString("Abilities", "Strength");
        //------------------------------------------

        //------------------jumpboost----------------
        $jumpboost = VanillaItems::DYE()->setColor(DyeColor::PURPLE());
        $jumpboost->setCustomName("§r§6JumpBoost");
        $jumpboost->setLore([
            "§2",
            "§f§7this item will give you Jump Boost VII for",
            "§7a few seconds",
            "§f",
            "§ePurcharse at §3" . Loader::getInstance()->getConfig()->get('tebex')
        ]);
        $jumpboost->getNamedTag()->setString("Abilities", "JumpBoost");
        //------------------------------------------

        //------------------speed----------------
        $speed = VanillaItems::DYE()->setColor(DyeColor::LIME());
        $speed->setCustomName("§r§6Speed");
        $speed->setLore([
            "§2",
            "§7this item will give you Speed II for",
            "§7a few seconds",
            "§f",
            "§ePurcharse at §3" . Loader::getInstance()->getConfig()->get('tebex')
        ]);
        $speed->getNamedTag()->setString("Abilities", "Speed");
        //------------------------------------------

        //------------------resistance----------------
        $resistance = VanillaItems::IRON_INGOT();
        $resistance->setCustomName("§r§dResistance III");
        $resistance->setLore([
            "§f",
            "§7this item will give you Resistence III for",
            "§7a few seconds",
            "§f",
            "§ePurcharse at §3" . Loader::getInstance()->getConfig()->get('tebex')
        ]);
        $resistance->getNamedTag()->setString("Abilities", "Resistance");
        //------------------------------------------

        //------------------regeneration----------------
        $regeneration = VanillaItems::GOLD_INGOT();
        $regeneration->setCustomName("§r§6Regeneration");
        $regeneration->setLore([
            "§2",
            "§7this item will give you Regeneration III for",
            "§7a few seconds",
            "§f",
            "§ePurcharse at §3" . Loader::getInstance()->getConfig()->get('tebex')
        ]);
        $regeneration->getNamedTag()->setString("Abilities", "Regeneration");
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
            "§ePurchase at §3" . Loader::getInstance()->getConfig()->get('tebex')
        ]);
        $ballofrange->getNamedTag()->setString("Abilities", "BallOfRange");
        //------------------------------------------

        //------------------FireWork----------------
        $firework = self::getItem(401, 0, 1);
        $firework->setCustomName("§r§3Firework");
        $firework->setLore([
            "§f",
            "§7This is the item capable of rising like",
            "§7the firework so you can escape from enemies",
            "§a",
            "§ePurchase at §3" . Loader::getInstance()->getConfig()->get('tebex')
        ]);
        $firework->getNamedTag()->setString("Abilities", "Firework");
        //------------------------------------------

        //------------------Berserk----------------
        $berserk = VanillaItems::DYE()->setColor(DyeColor::RED());
        $berserk->setCustomName("§r§cBerserk");
        $berserk->setLore([
            "§f",
            "§7Use this item to get Strength I, Resistance II",
            "§7and Speed II for seconds",
            "§a",
            "§ePurchase at §3" . Loader::getInstance()->getConfig()->get('tebex')
        ]);
        $berserk->getNamedTag()->setString("Abilities", "Berserk");
        //------------------------------------------

        //------------------Ninja Star----------------
        $Ninjastar = VanillaItems::NETHER_STAR();
        $Ninjastar->setCustomName("§r§bNinja Star");
        $Ninjastar->setLore([
            "§f",
            "§7Teleports you to the last person who",
            "§7hit you in the last 15 seconds!",
            "§a",
            "§ePurchase at §3" . Loader::getInstance()->getConfig()->get('tebex')
        ]);
        $Ninjastar->getNamedTag()->setString("Abilities", "NinjaStar");
        //------------------------------------------

        //-----------------------------------------------------
        $Samurai = VanillaItems::DIAMOND_SWORD();
        $Samurai->setCustomName("§r§4Samurai");
        $Samurai->setLore([
            "§f",
            "§7Use this item to get Samurai Strength II",
            "§7and Speed II for seconds",
            "§a",
            "§ePurchase at §3" . Loader::getInstance()->getConfig()->get('tebex')
        ]);
        $Samurai->getNamedTag()->setString("Abilities", "Samurai");
        //---------------------------------------------

        //-----------------------------------------------------
        $FocusMode = VanillaItems::GOLD_NUGGET();
        $FocusMode->setCustomName("§gFocus Mode");
        $FocusMode->setLore([
            "§f",
            "§7Increases enemy damage by 30%",
            "§a",
            "§ePurchase at §3" . Loader::getInstance()->getConfig()->get('tebex')
        ]);
        $FocusMode->getNamedTag()->setString("Abilities", "FocusMode");
        //---------------------------------------------

        //-----------------------------------------------------
        $PocketBard = VanillaItems::DYE()->setColor(DyeColor::ORANGE());
        $PocketBard->setCustomName("§6Pocket Bard");
        $PocketBard->setLore([
            "§f",
            "§7Opens a menu for you to select bard effect abilities",
            "§a",
            "§ePurchase at §3" . Loader::getInstance()->getConfig()->get('tebex')
        ]);
        $PocketBard->getNamedTag()->setString("Abilities", "PocketBard");
        //---------------------------------------------

        //----------------------------------------
        $potion = VanillaItems::POTION();
        $potion->setCustomName("§4Potion Refill");
        $potion->setLore([
            "§f",
            "§7Fills your entire inventory of health potions 2",
            "§a",
            "§ePurchase at §3" . Loader::getInstance()->getConfig()->get('tebex')
        ]);
        $potion->getNamedTag()->setString("Abilities", "Potion");
        //----------------------------------------------

        //--------------------------------------------------
        $Risky_Mode = VanillaItems::IRON_NUGGET();
        $Risky_Mode->setCustomName("§bRicky Mode");
        $Risky_Mode->setLore([
            "§7",
            "§7Use this item to get Ricky Mode Strength II, Resistance 3",
            "§a",
            "§ePurchase at §3" . Loader::getInstance()->getConfig()->get('tebex')
        ]);
        $Risky_Mode->getNamedTag()->setString("Abilities", "Risky_Mode");
        //---------------------------------------------

        //----------------------------------------
        $strom = VanillaItems::STONE_AXE();
        $strom->setCustomName("§6Strom Breaker");
        $Risky_Mode->setLore([
            "§7",
            "§7Use this item to get Ricky Mode Strength II, Resistance 3",
            "§a",
            "§ePurchase at §3" . Loader::getInstance()->getConfig()->get('tebex')
        ]);
        $Risky_Mode->getNamedTag()->setString("Abilities", "Strom_Breaker");
        //--------------------------------------------------

        $combo = VanillaItems::CLOWNFISH();
        $combo->setCustomName("§6Combo Ability");
        $combo->setLore([
            "§7",
            "§7Use this item to getC Combo Ability Strength II, Resistance II",
            "§a",
            "§ePurchase at §3" . Loader::getInstance()->getConfig()->get('tebex')
        ]);
        $combo->getNamedTag()->setString("Abilities", "Combo");

        //------------------Ninja Star----------------
        $ReverseNinja = VanillaItems::NETHER_STAR();
        $ReverseNinja->setCustomName("§r§bReverse Ninja");
        $ReverseNinja->setLore([
            "§f",
            "§7Teleports you to the last person who",
            "§7hit you in the last 15 seconds!",
            "§a",
            "§ePurchase at §3" . Loader::getInstance()->getConfig()->get('tebex')
        ]);
        $ReverseNinja->getNamedTag()->setString("Abilities", "ReverseNinja");
        //------------------------------------------

        //------------------Graphin Hook----------------
        $GraphinHook = VanillaItems::FISHING_ROD();
        $GraphinHook->setCustomName("§r§9Graphin Hook");
        $GraphinHook->setLore([
            "§f",
            "§7It drives you in the direction you are going",
            "§7Cooldown: &g1:00",
            "§a",
            "§ePurchase at §3" . Loader::getInstance()->getConfig()->get('tebex')
        ]);
        $GraphinHook->getNamedTag()->setString("Abilities", "GraphinHook");
        //------------------------------------------

        $menu->getInventory()->setItem(0, $GraphinHook);
        $menu->getInventory()->setItem(1, $ballofrange);
        $menu->getInventory()->setItem(2, $abilitydisabler);
        $menu->getInventory()->setItem(3, $exoticbone);
        $menu->getInventory()->setItem(4, $firework);
        $menu->getInventory()->setItem(5, $effectdisabler);
        $menu->getInventory()->setItem(6, $portalebard);
        $menu->getInventory()->setItem(7, $frezzergun);
        $menu->getInventory()->setItem(8, $berserk);
        $menu->getInventory()->setItem(10, $switcher);
        $menu->getInventory()->setItem(11, $jumpboost);
        $menu->getInventory()->setItem(12, $regeneration);
        $menu->getInventory()->setItem(13, $resistance);
        $menu->getInventory()->setItem(14, $speed);
        $menu->getInventory()->setItem(15, $strenght);
        $menu->getInventory()->setItem(16, $Ninjastar);
        $menu->getInventory()->setItem(20, $potion);
        $menu->getInventory()->setItem(21, $Risky_Mode);
        $menu->getInventory()->setItem(22, $ReverseNinja);
        $menu->getInventory()->setItem(23, $secondchance);
        $menu->getInventory()->setItem(24, $FocusMode);

        $menu->setListener(function (InvMenuTransaction $transaction): InvMenuTransactionResult {
            $player = $transaction->getPlayer();
            $transaction->getPlayer()->removeCurrentWindow();
            return $transaction->discard();
        });
        $menu->send($player);
    }

    public static function Prizes(Player $player): void
    {

        $menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
        $menu->setName("§l§b &fCONTENT");

        $menu->setListener(function (InvMenuTransaction $transaction): InvMenuTransactionResult {
            $player = $transaction->getPlayer();
            $transaction->getPlayer()->removeCurrentWindow();
            return $transaction->discard();
        });
        $menu->send($player);
    }

    public static function getItem($id, $meta = 0, $count = 1): Item
    {
        return LegacyStringToItemParser::getInstance()->parse("{$id}:{$meta}")->setCount($count);
    }
}
