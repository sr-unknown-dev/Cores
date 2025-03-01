<?php

namespace hcf\module\anticheat;

use CortexPE\DiscordWebhookAPI\Embed;
use CortexPE\DiscordWebhookAPI\Message;
use CortexPE\DiscordWebhookAPI\Webhook;
use hcf\Loader;
use hcf\player\Player;
use pocketmine\player\PlayerInfo;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as TF;

class AntiCheatManager {
    
    private Config $config;
    private Config $exemptConfig;
    private array $exemptPlayers = [];
    private array $alerts = [];

    public function __construct() {
        $this->config = Loader::getInstance()->getConfig();
        $this->exemptConfig = new Config(Loader::getInstance()->getDataFolder() . "exempt.yml", Config::YAML);
        $this->loadExemptPlayers();
    }

    private function loadExemptPlayers(): void {
        if ($this->exemptConfig->exists("players")) {
            $this->exemptPlayers = $this->exemptConfig->get("players", []);
        }
    }

    public function AlertStaff(Player $player, string $checkName, int $violations): void {
        if (!$player->isOnline()) return;

        $message = TF::colorize("§8[§cAntiCheat§8] §f" . $player->getName() . " §7failed §f" . 
                   $checkName . " §7(x" . $violations . ")");

        foreach (Server::getInstance()->getOnlinePlayers() as $staff) {
            if (!$staff instanceof Player) continue;
            if ($staff->hasPermission("anticheat.alerts") && $this->hasAlerts($staff)) {
                $staff->sendMessage($message);
            }
        }

        try {
            $webhookUrl = $this->config->getNested("alerts.webhook");
            if (!empty($webhookUrl)) {
                $webhook = new Webhook($webhookUrl);
                $msg = new Message();
                $embed = new Embed();
                $embed->setTitle($checkName . " Alert");
                $embed->setColor(0xf9ff1a);
                $embed->setDescription(
                    "Player: " . $player->getName() . 
                    "\nPing: " . $player->getNetworkSession()->getPing() .
                    "\nViolations: " . $violations
                );
                $embed->setFooter("Server Network");
                $msg->addEmbed($embed);
                $webhook->send($msg);
            }
        } catch (\Throwable $e) {
            Server::getInstance()->getLogger()->error("Failed to send webhook: " . $e->getMessage());
        }
    }

    public function toggleAlerts(Player $player): void {
        $name = $player->getName();
        
        if (isset($this->alerts[$name])) {
            unset($this->alerts[$name]);
            $player->sendMessage(TF::colorize("§8[§gAntiCheat§8] §fAlerts have been disabled."));
        } else {
            $this->alerts[$name] = true;
            $player->sendMessage(TF::colorize("§8[§gAntiCheat§8] §fAlerts have been enabled."));
        }
    }

    public function hasAlerts(Player $player): bool {
        return isset($this->alerts[$player->getName()]) && $player->hasPermission("anticheat.alerts");
    }

    public function toggleExemption(Player $player): void {
        $name = $player->getName();
        
        if (isset($this->exemptPlayers[$name])) {
            unset($this->exemptPlayers[$name]);
            $this->exemptConfig->set("players", $this->exemptPlayers);
            $player->sendMessage(TF::colorize("§8[§gAntiCheat§8] §fExemption has been removed."));
        } else {
            $this->exemptPlayers[$name] = true;
            $this->exemptConfig->set("players", $this->exemptPlayers);
            $player->sendMessage(TF::colorize("§8[§gAntiCheat§8] §fExemption has been added."));
        }
        $this->exemptConfig->save();
    }

    public function hasExemption(Player $player): bool {
        return isset($this->exemptPlayers[$player->getName()]);
    }

    public function punishments(Player $player, string $checkName): void {
        if (!$player->isOnline()) return;
        
        $staffMode = Loader::getInstance()->getStaffModeManager();
        if ($staffMode === null) return;

        switch (strtolower($checkName)) {
            case "speed":
            case "autoclick":
            case "reach":
                $staffMode->addBanAntiCheat($player, ucfirst($checkName), "30d");
                break;
        }
    }
}