<?php

declare(strict_types=1);

namespace hcf\handler\kit\command\subcommand;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use hcf\handler\kit\command\KitSubCommand;
use hcf\Loader;
use hcf\player\Player;
use hcf\utils\time\Timer;
use pocketmine\command\CommandSender;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\VanillaItems;
use pocketmine\permission\DefaultPermissions;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionManager;
use pocketmine\utils\TextFormat;

/**
 * Class CreateSubCommand
 * @package hcf\handler\kit\command\subcommand
 */
class CreateSubCommand extends BaseSubCommand
{
    public function __construct(string $name, string $description = "", array $aliases = [])
    {
        parent::__construct($name, $description, $aliases);
    }

    public function prepare(): void
    {
        $this->setPermission($this->getPermission());
        $this->registerArgument(0, new RawStringArgument("kitName"));
        $this->registerArgument(1, new RawStringArgument("nameFormat"));
        $this->registerArgument(2, new RawStringArgument("cooldown"));
        $this->registerArgument(3, new RawStringArgument("permission"));
        $this->registerArgument(4, new RawStringArgument("free|pay"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender instanceof Player)
            return;

        if (count($args) < 4) {
            $sender->sendMessage(TextFormat::colorize('&c/kit create [string: kitName] [string: nameFormat] [string: cooldown | optional] [string: permission | optional] [string: category | free|pay|op]'));
            return;
        }
        $kitName = $args["kitName"];
        $nameFormat = $args["nameFormat"];
        $cooldown = $args["cooldown"];
        $permission = $args["permission"];
        $category = $args["free|pay"];

        $items = $sender->getInventory()->getContents();
        $armor = $sender->getArmorInventory()->getContents();

        if (Loader::getInstance()->getHandlerManager()->getKitManager()->getKit($kitName) !== null) {
            $sender->sendMessage(TextFormat::colorize('&cThis kit already exists'));
            return;
        }
        $representativeItem = $sender->getInventory()->getItemInHand();
        $cooldown = Timer::time($cooldown);

        if ($category === "free") {
            Loader::getInstance()->getHandlerManager()->getKitManager()->addKit($kitName, $nameFormat, $permission, $representativeItem, $items, $armor, $cooldown);
        } elseif ($category === "pay") {
            Loader::getInstance()->getHandlerManager()->getKitPayManager()->addKit($kitName, $nameFormat, $permission, $representativeItem, $items, $armor, $cooldown);
        } elseif ($category === "op") {
            Loader::getInstance()->getHandlerManager()->getKitOpManager()->addKit($kitName, $nameFormat, $permission, $representativeItem, $items, $armor, $cooldown);
        }else {
            $sender->sendMessage(TextFormat::colorize("&cCategory is not valid use: &efree &cor &epay."));
        }
        $sender->sendMessage(TextFormat::colorize('&aYou have successfully created the ' . $kitName . ' kit'));

        Loader::getInstance()->getHandlerManager()->getKitManager()->registerPermission($permission);
    }

    public function getPermission(): ?string
    {
        return "kit.command.create";
    }
}