<?php

namespace hcf\timer\command;

use CortexPE\DiscordWebhookAPI\Embed;
use CortexPE\DiscordWebhookAPI\Message;
use CortexPE\DiscordWebhookAPI\Webhook;
use hcf\Loader;
use hcf\timer\types\TimerAirdrop;
use hcf\utils\time\Timer;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use pocketmine\utils\TextFormat as TE;

class AirdropCommand extends Command {
    
    /**
     * AirdropllCommand Constructor.
     */
    public function __construct(){
        parent::__construct('airdropall', 'Use command for auto airdrop');
        $this->setPermission("timer.all.command");
    }
    
    /**
     * @param CommandSender $sender
     * @param String $label
     * @param Array $args
     * @return void
     */

    public function execute(CommandSender $sender, string $commandLabel, array $args): void {

        if(count($args) === 0){
            $sender->sendMessage(TextFormat::colorize("&cUsar: /{$commandLabel} <on|off>"));
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
                if(TimerAirdrop::isEnable()){
                    $sender->sendMessage(" ".TE::RED."El evento se inició antes, ¡no puedes hacer esto!");
                    return;
                }
                $time = $args[1];
                
                $time = Timer::time($time);
                
                TimerAirdrop::start($time);
                $sender->sendMessage("§3Airdropall has started");
                $webHook = new Webhook(Loader::getInstance()->getConfig()->get('hcf.webhook'));
                $msg = new Message();
                $msg->setContent('<@&1184267398501629962>');
                $embed = new Embed();
                $embed->setTitle("AirdropAll has started");
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
                if(!TimerAirdrop::isEnable()){
                    $sender->sendMessage(TextFormat::colorize('&cEl evento nunca se inició, ¡no puedes hacer esto!'));
                    return;
                }
                TimerAirdrop::stop();
            break;
        }
    }
}

?>