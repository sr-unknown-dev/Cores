<?php

namespace unknown\commands;

use hcf\player\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use unknown\Loader;

class SetWhitelistStatusCommand extends Command
{
    public function __construct()
    {
        parent::__construct("swhitelist", "active or desactive the whitelist");
    }

    public function execute(CommandSender $sender, string $label, array $args): void
    {
        if (!$sender instanceof Player) return;

        if (count($args) < 2) {
            $sender->sendMessage(TextFormat::colorize("&cUso: /comando <on|off> <servidor>"));
            return;
        }

        $config = Loader::getInstance()->getConfig();
        $servers = $config->get('servers');

        $serverName = strtolower($args[1]);

        if (!isset($servers[$serverName])) {
            $sender->sendMessage(TextFormat::colorize("&cEse servidor no está en la configuración."));
            return;
        }

        switch (strtolower($args[0])) {
            case 'on':
                $config->setNested("servers.$serverName.whitelist", true);
                $sender->sendMessage(TextFormat::colorize("&aWhitelist activada para &e$serverName"));
                break;

            case 'off':
                $config->setNested("servers.$serverName.whitelist", false);
                $sender->sendMessage(TextFormat::colorize("&aWhitelist desactivada para &e$serverName"));
                break;

            default:
                $sender->sendMessage(TextFormat::colorize("&cUso: /comando <on|off> <servidor>"));
                return;
        }

        $config->save();
    }
}