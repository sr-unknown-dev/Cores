<?php

declare(strict_types=1);

namespace hcf\handler\kit\command\subcommand;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use hcf\handler\kit\command\KitSubCommand;
use hcf\player\Player;
use hcf\utils\inventorie\Inventories;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

/**
 * Class EditSubCommand
 * @package hcf\handler\kit\command\subcommand
 */
class EditSubCommand extends BaseSubCommand
{
    public function __construct(string $name, string $description = "", array $aliases = [])
    {
        parent::__construct($name, $description, $aliases);
    }

    public function prepare(): void
    {
        $this->setPermission($this->getPermission());
        $this->registerArgument(0, new RawStringArgument("free|play|op"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender instanceof Player)
            return;

        if (count($args) < 1) {
            $sender->sendMessage(TextFormat::colorize("&cUse: /kit edit [string: free|play|op]"));
            return;
        }

        if ($args["free|play|op"] === "free") {
            Inventories::editKitOrganization($sender);
        } elseif ($args["free|play|op"] === "pay") {
            Inventories::editKitPayOrganization($sender);
        } elseif ($args["free|play|op"] === "op") {
            Inventories::editKitOpOrganization($sender);
        }else {
            $sender->sendMessage(TextFormat::RED."Uso: /kit edit [string: free|play|op]");
        }
    }

    public function getPermission(): ?string
    {
        return "kit.command.edit";
    }
}