<?php

namespace daily\Utils;

use daily\Loader;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use pocketmine\block\utils\DyeColor;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;

class Inventorys {
    public static array $players = [];

    public static function DailyMenu(Player $player): void {
        $config = Utils::getConfig();
        $configItems = Utils::getItemsConfig();
        $data = $config->get($player->getName(), [
            "time" => 0,
            "daily1" => false,
            "daily2" => false,
            "daily3" => false,
            "daily4" => false,
            "daily5" => false
        ]);

        $time = $data["time"];
        $dailies = [
            ["time" => 86400, "key" => "daily1", "slot" => 11],
            ["time" => 172800, "key" => "daily2", "slot" => 12],
            ["time" => 259200, "key" => "daily3", "slot" => 13],
            ["time" => 345600, "key" => "daily4", "slot" => 14],
            ["time" => 432000, "key" => "daily5", "slot" => 15]
        ];

        $menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);

        foreach ($dailies as $daily) {
            $item = VanillaItems::DYE()->setColor(DyeColor::GRAY())->setCustomName("§cLook");
            if ($time >= $daily["time"] && !$data[$daily["key"]]) {
                $item = VanillaItems::DYE()->setColor(DyeColor::LIME())->setCustomName("§gDaily §f" . substr($daily["key"], -1));
            } elseif ($data[$daily["key"]]) {
                $item = VanillaItems::DYE()->setColor(DyeColor::GRAY())->setCustomName("§cReclaimed");
            } else {
                $remainingTime = $daily["time"] - $time;
                $formattedTime = self::formatTime($remainingTime);
                $item = VanillaItems::DYE()->setColor(DyeColor::GRAY())->setCustomName("§cLook\n§7" . $formattedTime);
            }
            $menu->getInventory()->setItem($daily["slot"], $item);
        }

        $menu->setListener(function (InvMenuTransaction $transaction) use ($menu, $config, $configItems, $player, &$data): InvMenuTransactionResult {
            $item = $transaction->getItemClicked();
            $customName = $item->getCustomName();
            $slot = $transaction->getAction()->getSlot();

            if (strpos($customName, "§gDaily §f") !== false) {
                $dailyNumber = substr($customName, -1);
                Loader::getInstance()->getServer()->broadcastMessage($player->getName() . " &athe Daily has claimed #" . $dailyNumber);

                $content = [];
                foreach ($configItems->get($dailyNumber, []) as $items) {
                    $content[] = Utils::deserialize($items);
                }
                foreach ($content as $item) {
                    $player->getInventory()->addItem($item);
                }
                $data["daily" . $dailyNumber] = true;
                $config->set($player->getName(), $data);
                $config->save();

                $menu->getInventory()->setItem($slot, VanillaItems::DYE()->setColor(DyeColor::GRAY())->setCustomName("§cLook"));
                return $transaction->discard();
            } elseif ($customName === "§cReclaimed") {
                $player->sendMessage("§cYou have already claimed this daily");
            } elseif ($customName === "§cLook") {
                $player->sendMessage("§cDaily not yet available");
            }
            return $transaction->discard();
        });

        $menu->send($player, "§6Daily Menu");
    }

    private static function formatTime(int $seconds): string {
        $days = floor($seconds / 86400);
        $hours = floor(($seconds % 86400) / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $seconds = $seconds % 60;
        return sprintf("%02d:%02d:%02d:%02d", $days, $hours, $minutes, $seconds);
    }
}