<?php

namespace unknown\menu;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\block\VanillaBlocks;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use unknown\Loader;

class Menu
{

    public static function send(Player $player): void
    {
        $menu = InvMenu::create(InvMenuTypeIds::TYPE_CHEST);
        $menu->setName(TextFormat::colorize("&l&gServer Selector"));

        $items = [
            'hcf' => self::createServerItem("HCF"),
            'kitmap' => self::createServerItem("KitMap"),
            'practice' => self::createServerItem("Practice"),
        ];

        $menu->getInventory()->setItem(10, $items['hcf']);
        $menu->getInventory()->setItem(13, $items['kitmap']);
        $menu->getInventory()->setItem(16, $items['practice']);

        $menu->setListener(function (InvMenuTransaction $transaction): InvMenuTransactionResult {
            $player = $transaction->getPlayer();
            $item = $transaction->getItemClicked();
            $name = strtolower(TextFormat::clean($item->getCustomName()));

            $config = Loader::getInstance()->getConfig();
            $queryManager = Loader::getInstance()->getQueryManager();

            if (!isset($config->get("servers")[$name])) {
                $player->sendMessage(TextFormat::colorize("&cServidor no encontrado."));
                return $transaction->discard();
            }

            $serverConfig = $config->get("servers")[$name];
            $status = $queryManager->getStatus($name);

            if ($status === null) {
                $player->sendMessage(TextFormat::colorize("&cEl servidor &4$name &cestá offline."));
                return $transaction->discard();
            }

            if ($serverConfig["whitelist"] === true || $serverConfig["whitelist"] === "on") {
                $player->sendMessage(TextFormat::colorize("&cEl servidor &4$name &cestá en whitelist."));
                return $transaction->discard();
            }

            $player->sendMessage(TextFormat::colorize("&aConectando a &2$name&a..."));
            $player->transfer($serverConfig["ip"], (int)$serverConfig["port"]);

            return $transaction->discard();
        });

        $menu->send($player);
    }

    private static function createServerItem(string $name)
    {
        $hcfstatus = Loader::getInstance()->getQueryManager()->getStatus('hcf');
        $kitmapstatus = Loader::getInstance()->getQueryManager()->getStatus('kitmap');
        $practicestatus = Loader::getInstance()->getQueryManager()->getStatus('practice');
        $hcf = $hcfstatus !== null ? "&7" . $hcfstatus['online'] . "/" . $hcfstatus['max'] : "&cOffline";
        $kitmap = $kitmapstatus !== null ? "&7" . $kitmapstatus['online'] . "/" . $kitmapstatus['max'] : "&cOffline";
        $practice = $practicestatus !== null ? "&7" . $practicestatus['online'] . "/" . $practicestatus['max'] : "&cOffline";
        $item = VanillaBlocks::MOB_HEAD()->asItem();
        $item->setCustomName(TextFormat::colorize('&l&g'.$name));

        if ($name === "HCF") {
            $item->setLore([
                '§l§gPlayers: §7' .$hcf,
                '§gMap Kit: Prot 1, Sharp 1'
        ]);
        }elseif ($name === "KitMap") {
            $item->setLore([
                '§l§gPlayers: §7' .$kitmap,
                '§gMap Kit: Prot 1, Sharp 1'
            ]);
        }elseif ($name === "Practice") {
            $item->setLore([
                '§l§gPlayers: §7' .$practice,
                '§gMap Kit: Prot 1, Sharp 1'
            ]);
        }

        return $item;
    }
}
