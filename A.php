<?php

namespace HubCore\Commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\scoreboard\Scoreboard;
use HubCore\Query\ServerQuery;

class NetworkCommand extends Command {
    private $plugin;

    public function __construct(PluginBase $plugin) {
        parent::__construct("network", "Ver el estado de la red", "/network", []);
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $label, array $args): void {
        if (!$sender instanceof Player) {
            $sender->sendMessage("§cEste comando solo puede ser usado dentro del juego.");
            return;
        }

        $config = new Config($this->plugin->getDataFolder() . "config.yml", Config::YAML);
        $servers = $config->get("servers", []);

        $scoreboard = new Scoreboard();
        $scoreboard->setTitle("§bNetwork Status");

        foreach ($servers as $name => $info) {
            $query = new ServerQuery($info["ip"], $info["port"]);
            $status = $query->query();

            $online = $status["players_online"] ?? 0;
            $maxPlayers = $status["max_players"] ?? 0;
            $serverStatus = $status["status"] === "On" ? "§aOnline" : "§cOffline";

            $scoreboard->addLine("§7{$name}: {$serverStatus} ({$online}/{$maxPlayers})");
        }

        $scoreboard->sendToPlayer($sender);
    }
}

?>