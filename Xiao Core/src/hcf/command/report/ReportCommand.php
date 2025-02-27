<?php

namespace hcf\command\report;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseCommand;
use hcf\arguments\PlayersArgument;
use hcf\Loader;
use hcf\player\Player;
use pocketmine\command\CommandSender;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

class ReportCommand extends BaseCommand
{
    private Config $reports;
    private array $cooldowns = [];

    public function __construct(string $name, string $description = "")
    {
        parent::__construct(Loader::getInstance(), $name, $description);
        $this->reports = new Config(Loader::getInstance()->getDataFolder() . "reports.yml", Config::YAML);
    }

    protected function prepare(): void
    {
        $this->setPermission("use.player.command");
        $this->registerArgument(0, new PlayersArgument("player", true));
        $this->registerArgument(1, new RawStringArgument("reason", true));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!isset($args["player"]) || !isset($args["reason"])) {
            $sender->sendMessage(TextFormat::YELLOW."Use: /report (player) (reason)");
            return;
        }

        $player = $args["player"];
        $reason = $args["reason"];

        if (!$player instanceof Player) {
            $sender->sendMessage(TextFormat::RED . "El jugador no está en línea o el nombre está mal escrito.");
            return;
        }

        if (empty($reason)) {
            $sender->sendMessage(TextFormat::RED . "Por favor, proporciona una razón para el reporte.");
            return;
        }

        $this->addReport($sender, $player, $reason);

        $sender->sendMessage(TextFormat::colorize("&7-----------------------------------------------------------------------------------------------------"));
        $sender->sendMessage(TextFormat::colorize("&aHas reportado con éxito a &b" . $player->getName()));
        $sender->sendMessage(TextFormat::colorize("&aEspera a que un staff vea tu reporte o también puedes reportar desde ticket en nuestro discord"));
        $sender->sendMessage(TextFormat::colorize("&9Discord: &fhttps://discord.com/channels/1167871989579518074/1236875301745590302/1236876082997760052"));
        $sender->sendMessage(TextFormat::colorize("&7-----------------------------------------------------------------------------------------------------"));
    }

    private function addReport(CommandSender $reporter, Player $target, string $reason): void
    {
        $id = uniqid();
        $this->reports->set($id, [
            "player" => $reporter->getName(),
            "target" => $target->getName(),
            "reason" => $reason,
            "timestamp" => time()
        ]);
        $this->reports->save();
    }

    public function getPermission(): string
    {
        return "use.player.command";
    }
}