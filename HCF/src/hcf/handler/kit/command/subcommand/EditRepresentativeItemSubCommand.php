<?php

namespace hcf\handler\kit\command\subcommand;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use hcf\handler\kit\command\KitSubCommand;
use hcf\Loader;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class EditRepresentativeItemSubCommand extends BaseSubCommand
{
    public function __construct(string $name, string $description = "", array $aliases = [])
    {
        parent::__construct($name, $description, $aliases);
    }

    public function prepare(): void
    {
        $this->setPermission($this->getPermission());
        $this->registerArgument(0, new RawStringArgument("kitName"));
        $this->registerArgument(1, new RawStringArgument("free|play|op"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (count($args) < 2) {
            $sender->sendMessage(TextFormat::RED."Uso: /kit setitem [string: kitname] [category: free|play|op]");
            return;
        }

        $category = $args["free|play|op"];

        if ($category === "free") {
            $kitManager = Loader::getInstance()->getHandlerManager()->getKitManager();
            $kit = $kitManager->getKit($args["kitName"]);
            if ($kit === null) {
                $sender->sendMessage(TextFormat::colorize("&cGkit Invalido"));
                return;
            }
            $item = $sender->getInventory()->getItemInHand();
            $kit->setRepresentativeItem($item);
            $sender->sendMessage(TextFormat::colorize("&aHas cambiado con exito el representative item del gkit"));
        } elseif ($category === "pay") {
            $kitManager = Loader::getInstance()->getHandlerManager()->getKitPayManager();
            $kit = $kitManager->getKit($args["kitName"]);
            if ($kit === null) {
                $sender->sendMessage(TextFormat::colorize("&cGkit Invalido"));
                return;
            }
            $item = $sender->getInventory()->getItemInHand();
            $kit->setRepresentativeItem($item);
            $sender->sendMessage(TextFormat::colorize("&aHas cambiado con exito el representative item del gkit"));
        } elseif ($category === "op") {
            $kitManager = Loader::getInstance()->getHandlerManager()->getKitOpManager();
            $kit = $kitManager->getKit($args["kitName"]);
            if ($kit === null) {
                $sender->sendMessage(TextFormat::colorize("&cGkit Invalido"));
                return;
            }
            $item = $sender->getInventory()->getItemInHand();
            $kit->setRepresentativeItem($item);
            $sender->sendMessage(TextFormat::colorize("&aHas cambiado con exito el representative item del gkit"));
        }else {
            $sender->sendMessage(TextFormat::RED."Uso: /kit setitem [string: kitname] [category: free|play|op]");
        }
    }

    public function getPermission(): ?string
    {
        return "kit.command.setitem";
    }
}