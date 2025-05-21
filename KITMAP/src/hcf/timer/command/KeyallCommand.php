<?php

namespace hcf\timer\command;

use hcf\timer\types\TimerKey;
use hcf\utils\time\Timer;
use hcf\Loader;
use CortexPE\DiscordWebhookAPI\Embed;
use CortexPE\DiscordWebhookAPI\Message;
use CortexPE\DiscordWebhookAPI\Webhook;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use pocketmine\utils\TextFormat as TE;

class KeyallCommand extends Command {
    
    /**
     * KeyallCommand Constructor.
     */
    public function __construct(){
        parent::__construct('keyall', 'Use command for auto keyall');
        $this->setPermission("timer.all.command");
    }
    
    /**
     * @param CommandSender $sender
     * @param String $label
     * @param Array $args
     * @return void
     */

    public function execute(CommandSender $sender, string $label, array $args): void {
        if(count($args) === 0){
            $sender->sendMessage(TextFormat::colorize("&cUsar: /{$label} <on|off>"));
            return;
        }
        if (!$sender->hasPermission("timer.all.command")) {
            $sender->sendMessage(TextFormat::colorize('&cNo tienes permisos para usar este comando'));
            return;
        }

        switch($args[0]){
            case "on":
                if(empty($args[1])){
                    $sender->sendMessage(" ".TE::RED."Usar: /{$label} {$args[0]} [Int: time]");
                    return;
                }
                if(TimerKey::isEnable()){
                    $sender->sendMessage(" ".TE::RED."El evento se inició antes, ¡no puedes hacer esto!");
                    return;
                }
                $time = $args[1];
                
                $time = Timer::time($time);
                
                TimerKey::start($time);
                $sender->sendMessage("§aKeyall has started");
                Loader::getInstance()->getServer()->broadcastMessage(TE::colorize("&7███████"));
                Loader::getInstance()->getServer()->broadcastMessage(TE::colorize("&7█&a█&7███&a█&7█"));
                Loader::getInstance()->getServer()->broadcastMessage(TE::colorize("&7█&a█&7██&a█&7██ &r&7[&2&lkitmap &l&aKEYALL&r&7]"));
                Loader::getInstance()->getServer()->broadcastMessage(TE::colorize("&7█&a███&7███ &r&aKeyall &ghas starter for: &f".Timer::Format($time)));
                Loader::getInstance()->getServer()->broadcastMessage(TE::colorize("&7█&a█&7██&a█&7██ "));
                Loader::getInstance()->getServer()->broadcastMessage(TE::colorize("&7█&a█&7███&a█&7█"));
                Loader::getInstance()->getServer()->broadcastMessage(TE::colorize("&7█&a█&7███&a█&7█"));
                Loader::getInstance()->getServer()->broadcastMessage(TE::colorize("&7███████"));
                $webHook = new Webhook(Loader::getInstance()->getConfig()->get('hcf.webhook'));
                $msg = new Message();
                $msg->setContent('<@&1184267398501629962>');
                $embed = new Embed();
                $embed->setTitle("Keyall has started");
                $embed->setColor(0xf9ff1a);
                $embed->setDescription("⏳Time: ".Timer::Format($time)."\nIp: kitmap.ddns.net\nPort: 25576\nStore: https://kitmap.tebex.io/");
         
                $embed->setFooter("kitmap");
                $msg->addEmbed($embed);
                
                $webHook->send($msg);
            break;
            case "off":
                if (!$sender->hasPermission("timer.all.command")) {
                    $sender->sendMessage(TextFormat::colorize('&cNo tienes permisos para usar este comando'));
                    return;
                }
                if(!TimerKey::isEnable()){
                    $sender->sendMessage(TextFormat::colorize('&cEl evento nunca se inició, ¡no puedes hacer esto!'));
                    return;
                }
                TimerKey::stop();
            break;
        }
    }
}

?>