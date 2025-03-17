<?php

declare(strict_types=1);

namespace hcf\handler\kit\command\subcommand;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use hcf\handler\kit\command\KitSubCommand;
use hcf\Loader;
use hcf\player\Player;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

/**
 * Class DeleteSubCommand
 * @package hcf\handler\kit\command\subcommand
 */
class DeleteSubCommand extends BaseSubCommand
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
        if (!$sender instanceof Player)
            return;

        if (count($args) < 2) {
            $sender->sendMessage(TextFormat::colorize('&c/kit delete [string: kitName] [category: free|play|op]'));
            return;
        }
        $kitName = $args["kitName"];
        $category = $args["free|play|op"];

        if ($category === "free") {
            if (Loader::getInstance()->getHandlerManager()->getKitManager()->getKit($kitName) === null) {
                $sender->sendMessage(TextFormat::colorize('&cThis kit does not exist'));
                return;
            }
            Loader::getInstance()->getHandlerManager()->getKitManager()->removeKit($kitName);
            $sender->sendMessage(TextFormat::colorize('&cYou have successfully removed kit ' . $kitName));
        } elseif ($category === "pay") {
            if (Loader::getInstance()->getHandlerManager()->getKitPayManager()->getKit($kitName) === null) {
                $sender->sendMessage(TextFormat::colorize('&cThis kit does not exist'));
                return;
            }
            Loader::getInstance()->getHandlerManager()->getKitPayManager()->removeKit($kitName);
            $sender->sendMessage(TextFormat::colorize('&cYou have successfully removed kit ' . $kitName));
        } elseif ($category === "op") {
            if (Loader::getInstance()->getHandlerManager()->getKitOpManager()->getKit($kitName) === null) {
                $sender->sendMessage(TextFormat::colorize('&cThis kit does not exist'));
                return;
            }
            Loader::getInstance()->getHandlerManager()->getKitOpManager()->removeKit($kitName);
            $sender->sendMessage(TextFormat::colorize('&cYou have successfully removed kit ' . $kitName));
        }else {
            $sender->sendMessage(TextFormat::colorize("&cCategory is not valid use: &efree &cor &epay."));
        }
    }

    public function getPermission(): ?string
    {
        return "kit.command.delete";
    }
}