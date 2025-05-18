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
use unknown\query\QueryStatus;

class Menu
{

    public static function send(Player $player): void {
        $menu = InvMenu::create(InvMenu::TYPE_CHEST);
        $menu->setName(TextFormat::colorize("&l&gServer Selector"));

        $config = Loader::getInstance()->getConfig();

        $servers = [
            'hcf' => ["name" => "HCF", "query" => new QueryStatus($config->getNested('servers.hcf.ip'), $config->getNested('servers.hcf.port'))],
            'kitmap' => ["name" => "KitMap", "query" => new QueryStatus($config->getNested('servers.kitmap.ip'), $config->getNested('servers.kitmap.port'))],
            'practice' => ["name" => "Practice", "query" => new QueryStatus($config->getNested('servers.practice.ip'), $config->getNested('servers.practice.port'))]
        ];

        foreach ($servers as $key => $server) {
            $queryResult = $server["query"]->query();
            $status = $queryResult["status"] === "On" ? "&7{$queryResult['players_online']}/{$queryResult['max_players']}" : "&cOffline";
            $servers[$key]["item"] = self::createServerItem($server["name"], $status);
        }

        $menu->getInventory()->setItem(10, $servers['hcf']["item"]);
        $menu->getInventory()->setItem(13, $servers['kitmap']["item"]);
        $menu->getInventory()->setItem(16, $servers['practice']["item"]);

        $menu->setListener(function (InvMenuTransaction $transaction): InvMenuTransactionResult {
            $player = $transaction->getPlayer();
            $item = $transaction->getItemClicked();
            $name = strtolower(TextFormat::clean($item->getCustomName()));

            $config = Loader::getInstance()->getConfig();

            if (!isset($config->get("servers")[$name])) {
                $player->sendMessage(TextFormat::colorize("&cServidor no encontrado."));
                return $transaction->discard();
            }

            $serverConfig = $config->get("servers")[$name];
            $queryStatus = new QueryStatus($serverConfig["ip"], $serverConfig["port"]);
            $queryResult = $queryStatus->query();

            if ($queryResult["status"] !== "On") {
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
        $item = VanillaBlocks::MOB_HEAD()->asItem();
        $item->setCustomName(TextFormat::colorize('&l&g'.$name));

        if ($name === "HCF") {
            $item->setLore([
                '§l§gPlayers: §7' .QueryStatus::infoHCF()['players'],
                '§gMap Kit: Prot 1, Sharp 1',
                '§gStatus&7: '.QueryStatus::infoHCF()['status'],
        ]);
        }elseif ($name === "KitMap") {
            $item->setLore([
                '§l§gPlayers: §7' . QueryStatus::infoKitMap()['players'],
                '§gMap Kit: Prot 1, Sharp 1',
                '§gStatus&7: '.QueryStatus::infoKitMap()['status'],
            ]);
        }elseif ($name === "Practice") {
            $item->setLore([
                '§l§gPlayers: §7' . QueryStatus::infoPractice()['players'],
                '§gMap Kit: Prot 1, Sharp 1',
                '§gStatus&7: '.QueryStatus::infoPractice()['status'],
            ]);
        }

        return $item;
    }
}
