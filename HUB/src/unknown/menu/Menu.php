<?php

namespace unknown\menu;

use hcf\utils\item\Items;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\item\VanillaItems;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use unknown\Loader;

class Menu {

    public function send(Player $player): void {
        $menu = InvMenu::create(InvMenuTypeIds::TYPE_CHEST);
        $menu->setName(TextFormat::colorize("&l&gServer Selector"));

        $items = [
            'hcf' => $this->createServerItem("HCF"),
            'kitmap' => $this->createServerItem("KitMap"),
            'practice' => $this->createServerItem("Practice"),
        ];

        $menu->getInventory()->setItem(10, $items['hcf']);
        $menu->getInventory()->setItem(13, $items['kitmap']);
        $menu->getInventory()->setItem(16, $items['practice']);

        $menu->setListener(function(InvMenuTransaction $transaction): InvMenuTransactionResult {
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

    private function createServerItem(string $name){
        $item = VanillaItems::PLAYER_HEAD();
        $item->setCustomName(TextFormat::colorize("&r&b{$name}"));

        $item->setLore([]);
    }
}
