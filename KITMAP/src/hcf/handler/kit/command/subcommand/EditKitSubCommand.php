<?php

namespace hcf\handler\kit\command\subcommand;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use hcf\handler\kit\command\KitSubCommand;
use hcf\Loader;
use muqsit\invmenu\InvMenu;
use pocketmine\command\CommandSender;
use pocketmine\inventory\Inventory;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class EditKitSubCommand extends BaseSubCommand
{
    public function __construct(string $name, string $description = "", array $aliases = [])
    {
        parent::__construct($name, $description, $aliases);
    }

    public function prepare(): void
    {
        $this->setPermission($this->getPermission());
        $this->registerArgument(0, new RawStringArgument("free|play|op"));
        $this->registerArgument(1, new RawStringArgument("items|armor"));
        $this->registerArgument(2, new RawStringArgument("kitName"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (count($args) < 3) {
            $sender->sendMessage(TextFormat::RED."Uso: /kit editcontent [string: free|play|op] [string: items|armor] [string: kitname]");
            return;
        }

        if ($args["free|play|op"] === "free") {
            $kitManager = Loader::getInstance()->getHandlerManager()->getKitManager();
            $loots = $kitManager->getKit($args["kitName"]);
            if ($loots === null) {
                $sender->sendMessage(TextFormat::colorize("&cGkit Invalido"));
                return;
            }
            if ($args["items|armor"] === "items") {
                $chest = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
                $chest->setName(TextFormat::colorize("&9GKit &7". ($args["kitName"]) . "Items"));
                if ($loots->getItems() !== null) {
                    foreach ($loots->getItems() as $slot => $item) {
                        $chest->getInventory()->setItem($slot, $item);
                    }
                }
                $chest->setInventoryCloseListener(function (Player $player, Inventory $inventory) use ($loots): void {
                    $loots->setItems($inventory->getContents());
                    $player->sendMessage(TextFormat::colorize("&aThe Gkit loot items has been modified correctly"));
                });
                $chest->send($sender);
                return;
            } elseif ($args["items|armor"] === "armor") {
                $chest = InvMenu::create(InvMenu::TYPE_HOPPER);
                $chest->setName(TextFormat::colorize("&9GKit &7". ($args["kitName"]) . "Armor"));
                if ($loots->getArmor() !== null) {
                    foreach ($loots->getArmor() as $slot => $item) {
                        $chest->getInventory()->setItem($slot, $item);
                    }
                }
                $chest->setInventoryCloseListener(function (Player $player, Inventory $inventory) use ($loots): void {
                    $loots->setArmor($inventory->getContents());
                    $player->sendMessage(TextFormat::colorize("&aThe Gkit loot armor has been modified correctly"));
                });
                $chest->send($sender);
                return;
            }
        } elseif ($args["free|play|op"] === "pay") {
            $kitManager = Loader::getInstance()->getHandlerManager()->getKitPayManager();
            $loots = $kitManager->getKit($args["kitName"]);
            if ($loots === null) {
                $sender->sendMessage(TextFormat::colorize("&cGkit Invalido"));
                return;
            }
            if ($args["items|armor"] === "items") {
                $chest = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
                $chest->setName(TextFormat::colorize("&9GKit &7". ($args["kitName"]) . "Items"));
                if ($loots->getItems() !== null) {
                    foreach ($loots->getItems() as $slot => $item) {
                        $chest->getInventory()->setItem($slot, $item);
                    }
                }
                $chest->setInventoryCloseListener(function (Player $player, Inventory $inventory) use ($loots): void {
                    $loots->setItems($inventory->getContents());
                    $player->sendMessage(TextFormat::colorize("&aThe Gkit loot items has been modified correctly"));
                });
                $chest->send($sender);
                return;
            } elseif ($args["items|armor"] === "armor") {
                $chest = InvMenu::create(InvMenu::TYPE_HOPPER);
                $chest->setName(TextFormat::colorize("&9GKit &7". ($args["kitName"]) . "Armor"));
                if ($loots->getArmor() !== null) {
                    foreach ($loots->getArmor() as $slot => $item) {
                        $chest->getInventory()->setItem($slot, $item);
                    }
                }
                $chest->setInventoryCloseListener(function (Player $player, Inventory $inventory) use ($loots): void {
                    $loots->setArmor($inventory->getContents());
                    $player->sendMessage(TextFormat::colorize("&aThe Gkit loot armor has been modified correctly"));
                });
                $chest->send($sender);
                return;
            }
        } elseif ($args["free|play|op"] === "op") {
            $kitManager = Loader::getInstance()->getHandlerManager()->getKitOpManager();
            $loots = $kitManager->getKit($args["kitName"]);
            if ($loots === null) {
                $sender->sendMessage(TextFormat::colorize("&cGkit Invalido"));
                return;
            }
            if ($args["items|armor"] === "items") {
                $chest = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
                $chest->setName(TextFormat::colorize("&9GKit &7". ($args["kitName"]) . "Items"));
                if ($loots->getItems() !== null) {
                    foreach ($loots->getItems() as $slot => $item) {
                        $chest->getInventory()->setItem($slot, $item);
                    }
                }
            }elseif ($args["items|armor"] === "armor") {
                $chest = InvMenu::create(InvMenu::TYPE_HOPPER);
                $chest->setName(TextFormat::colorize("&9GKit &7". ($args["kitName"]) . "Armor"));
                if ($loots->getArmor() !== null) {
                    foreach ($loots->getArmor() as $slot => $item) {
                        $chest->getInventory()->setItem($slot, $item);
                    }
                }
            }
        }else {
            $sender->sendMessage(TextFormat::RED."Uso: /kit editcontent [items|armor] [string: kitname]");
        }
        $sender->sendMessage(TextFormat::RED."Uso: /kit editcontent [items|armor] [string: kitname]");
    }

    public function getPermission(): ?string
    {
        return "kit.command.edit";
    }
}