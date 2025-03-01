<?php

namespace hcf\koth\task;

use CortexPE\DiscordWebhookAPI\Embed;
use CortexPE\DiscordWebhookAPI\Message;
use CortexPE\DiscordWebhookAPI\Webhook;
use hcf\Loader;
use hcf\utils\time\Timer;
use hcf\utils\Utils;
use pocketmine\scheduler\Task;
use pocketmine\utils\TextFormat;

class AutoKothTask extends Task {

    public function onRun(): void {
        foreach(Loader::getInstance()->getKothManager()->getKoths() as $name => $data){
            if (Loader::getInstance()->getKothManager()->getKothActive() === null) {
                $koth = Loader::getInstance()->getKothManager()->getKoth($name);
                $location = Loader::getInstance()->getKothManager()->getKoth($name)->getCoords();
                $time = Loader::getInstance()->getKothManager()->getKoth($name)->getTime() / 60;
                $points = Loader::getInstance()->getKothManager()->getKoth($name)->getPoints();
                if ($koth->getName() !== "Citadel") {
                    Loader::getInstance()->getKothManager()->setKothActive($name);
                    
                    $webHook = new Webhook(Loader::getInstance()->getConfig()->get('koth.webhook'));

                    Loader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&8█&7███████&8█"));
                    Loader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&7██&4█&7███&4█&7██"));
                    Loader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&7██&4█&7██&4█&7███       &r&4".$koth->getName()." &r&7(".Timer::format($koth->getTime())."&7)"));
                    Loader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&7██&4█&7█&4█&7████       &r&7has been started"));
                    Loader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&7██&4██&7█████"));
                    Loader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&7██&4█&7█&4█&7████       &r&gLocation:"));
                    Loader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&7██&4█&7██&4█&7███       &r&7× World ".$koth->getCoords()." &7×"));
                    Loader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&7██&4█&7███&4█&7██"));
                    Loader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&8█&7███████&8█"));
                    Utils::kothstart();
        
        
                    $msg = new Message();
                    
                    $embed = new Embed();
                    $msg->setContent("@");
                    $embed->setTitle("KotH Automatic " . $name . " has started 🏔️");
                    $embed->setColor(0x9AD800);
                    $embed->addField("Location 📍", "{$location}");
                    $embed->addField("Time 🕐", "{$time} minutes", true);
                    $embed->addField("Rewards 🔑", "{$points} Points", true);
                    $embed->setFooter("");
                    $msg->addEmbed($embed);
        
        
                    $webHook->send($msg);
                }
            }
        }
    }

}