<?php

namespace unknown\menu;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use pocketmine\block\VanillaBlocks;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use unknown\Loader;
use unknown\query\QueryStatus;

class Menu
{
    public static function send(Player $player): void
    {
        $menu = InvMenu::create(InvMenu::TYPE_CHEST);
        $menu->setName(TextFormat::colorize("&l&gServer Selector"));

        // Crear los ítems de cada servidor
        $menu->getInventory()->setItem(10, self::createServerItem("HCF"));
        $menu->getInventory()->setItem(13, self::createServerItem("KitMap"));
        $menu->getInventory()->setItem(16, self::createServerItem("Practice"));

        $menu->setListener(function (InvMenuTransaction $transaction): InvMenuTransactionResult {
            $player = $transaction->getPlayer();
            $item = $transaction->getItemClicked();
            $name = strtolower(TextFormat::clean($item->getCustomName()));

            $config = Loader::getInstance()->getConfig();

            if (!isset($config->get("servers")[$name])) {
                $player->sendMessage(TextFormat::colorize("&cServidor no encontrado."));
                return $transaction->discard();
            }

            // Obtener el estado del servidor según el nombre
            switch ($name) {
                case "hcf":
                    $queryResult = QueryStatus::infoHCF();
                    break;
                case "kitmap":
                    $queryResult = QueryStatus::infoKitMap();
                    break;
                case "practice":
                    $queryResult = QueryStatus::infoPractice();
                    break;
                default:
                    $queryResult = ["status" => "§4Offline"];
                    break;
            }

            if ($queryResult["status"] !== TextFormat::GREEN . "Online") {
                $player->sendMessage(TextFormat::colorize("&cEl servidor &4$name &cestá offline."));
                return $transaction->discard();
            }

            $serverConfig = $config->get("servers")[$name];

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
        $item->setCustomName(TextFormat::colorize('&l&g' . $name));

        switch ($name) {
            case "HCF":
                $info = QueryStatus::infoHCF();
                break;
            case "KitMap":
                $info = QueryStatus::infoKitMap();
                break;
            case "Practice":
                $info = QueryStatus::infoPractice();
                break;
            default:
                $info = ["players" => "0/0", "status" => "§4Offline"];
                break;
        }

        $item->setLore([
            '§l§gPlayers: §7' . $info['players'],
            '§gMap Kit: Prot 1, Sharp 1',
            '§gStatus&7: ' . $info['status'],
        ]);

        return $item;
    }
}