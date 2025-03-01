<?php

declare(strict_types=1);

namespace hcf\koth\command\subcommand;

use CortexPE\DiscordWebhookAPI\Embed;
use CortexPE\DiscordWebhookAPI\Message;
use CortexPE\DiscordWebhookAPI\Webhook;
use hcf\koth\command\KothSubCommand;
use hcf\Loader;
use hcf\player\Player;
use hcf\utils\time\Timer;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

/**
 * Class StartSubCommand
 * @package hcf\koth\command\subcommand
 */
class StartSubCommand implements KothSubCommand
{
    
    /**
     * @param CommandSender $sender
     * @param array $args
     */
    public function execute(CommandSender $sender, array $args): void
    {
        if (count($args) < 1) {
            $sender->sendMessage(TextFormat::colorize('&c/koth start [string: name]'));
            return;
        }
        $name = $args[0];
        
        if (Loader::getInstance()->getKothManager()->getKothActive() !== null) {
            $sender->sendMessage(TextFormat::colorize('&cThere is already activated a koth right now'));
            return;
        }
        
        if (Loader::getInstance()->getKothManager()->getKoth($name) === null) {
            $sender->sendMessage(TextFormat::colorize('&cThe koth does not exist'));
            return;
        }
        $koth = Loader::getInstance()->getKothManager()->getKoth($name);
        $location = Loader::getInstance()->getKothManager()->getKoth($name)->getCoords();
        $time = Loader::getInstance()->getKothManager()->getKoth($name)->getTime() / 60;
        $points = Loader::getInstance()->getKothManager()->getKoth($name)->getPoints();
        
        if ($koth->getCapzone() === null) {
            $sender->sendMessage(TextFormat::colorize('&cThe capzone is not selected'));
            return;
        }

        if ($koth->getName() !== "Citadel") {
            Loader::getInstance()->getKothManager()->setKothActive($name);
            $sender->sendMessage(TextFormat::colorize('&aYou have activated the koth ' . $name));

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


            $msg = new Message();

            $msg->setContent("KoTH Started, use /koth list <&1184267396542894132>");
            
            $embed = new Embed();
            $msg->setContent("@everyone");
            $embed->setTitle("KotH " . $name . " has started🍁");
            $embed->setColor(0x9AD800);
            $embed->addField("Location ⚓", "{$location}");
            $embed->addField("Time 🕐", "{$time} minutes", true);
            $embed->addField("Rewards 🎁", "{$points} Points and 4 keys KoTH", true);
            $embed->setFooter("");
            $msg->addEmbed($embed);


            $webHook->send($msg);
        }
        if ($koth->getName() === "Citadel") {
            Loader::getInstance()->getKothManager()->setKothActive($name);
            $sender->sendMessage(TextFormat::colorize('&aYou have activated the koth ' . $name));

            $webHook = new Webhook(Loader::getInstance()->getConfig()->get('koth.webhook'));
            
            
            
            Loader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&7███████"));
            Loader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&7██&5████&7█"));
            Loader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&7█&5█&7█████ &r&6[Citadel]"));
            Loader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&7█&5█&7█████ &r&ehas started in &6" . $koth->getCoords() . "!"));
            Loader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&7█&5█&7█████ &r&6&eWin the event and get &9Rewards"));
            Loader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&7█&5█&7█████"));
            Loader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&7██&5████&7█"));
            Loader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&7███████"));


            $msg = new Message();

            $msg->setContent("KoTH Citadel, join the server @everyone");

            $embed = new Embed();
            $msg->setContent("@everyone");
            $embed->setTitle("Citadel has started 🌌");
            $embed->setColor(0xC13DFF);
            $embed->addField("Location 📍", "{$location}");
            $embed->addField("Time 🕐", "{$time} minutes", true);
            $embed->addField("Rewards 🔑", "{$points} Points", true);
            $embed->setFooter("");
            $msg->addEmbed($embed);


            $webHook->send($msg);
        }
    }
}