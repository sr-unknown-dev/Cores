<?php

namespace hcf\command;

use CortexPE\DiscordWebhookAPI\Embed;
use CortexPE\DiscordWebhookAPI\Message;
use CortexPE\DiscordWebhookAPI\Webhook;
use hcf\Loader;
use hcf\player\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class ReportCommand extends Command
{
    public function __construct()
    {
        parent::__construct("report", "Reporta un jugador a los staffs", "/report (player) (reason)");
        $this->setPermission("use.player.command");
    }

    public function execute(CommandSender $sender, string $label, array $args)
    {

        if (count($args) < 0) {
            $sender->sendMessage(TextFormat::YELLOW."Use: /report (player) (reason)");
            return;
        }
        
        if (isset($args[0])) {
            $player = Loader::getInstance()->getServer()->getPlayerExact($args[0]);

        if($player === null) {
            $sender->sendMessage(TextFormat::RED."El jugador no está en línea o el nombre está mal escrito.");
            return;
        }

        $reason = implode(" ", $args);
        $name = $player->getName();

        if(empty($reason)) {
            $sender->sendMessage(TextFormat::RED."Por favor, proporciona una razón para el reporte.");
            return;
        }

        if (empty($player)){
            $sender->sendMessage(TextFormat::RED."Por favor, proporciona una nombre para el reporte.");
            return;
        }

        if ($player === $sender->getName()){
            $sender->sendMessage(TextFormat::RED."No te puedes reportar a ti mismo.");
            return;
        }

        if ($reason){
            $sender->sendMessage(TextFormat::colorize("&7-----------------------------------------------------------------------------------------------------"));
            $sender->sendMessage(TextFormat::colorize("&aHas reportado con exito a &b".$name));
            $sender->sendMessage(TextFormat::colorize("&aEspera a que un staff vea tu reporte o tambien puedes reportar desde ticket en nuestro discord"));
            $sender->sendMessage(TextFormat::colorize("&9Discord: &fhttps://discord.com/channels/1167871989579518074/1236875301745590302/1236876082997760052"));
            $sender->sendMessage(TextFormat::colorize("&7-----------------------------------------------------------------------------------------------------"));
            $webHook = new Webhook(Loader::getInstance()->getConfig()->get('report.webhook'));

            $msg = new Message();

            $embed = new Embed();
            $embed->setTitle("New Report");
            $embed->setColor(0xD87200);
            $embed->setDescription("``👮Name:`` ".$name."\n``Reason:`` ".$reason."\n``Reported by:`` ".$sender->getName());
            $embed->setFooter("Report hcf");
            $msg->setContent("<@&1184267327538212924>");
            $msg->addEmbed($embed);

            $webHook->send($msg);
        }
        }
    }
}