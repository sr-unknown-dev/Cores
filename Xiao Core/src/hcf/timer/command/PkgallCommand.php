<?php

namespace hcf\timer\command;

use hcf\timer\types\TimerPackages;
use hcf\utils\time\Timer;
use hcf\Loader;
use CortexPE\DiscordWebhookAPI\Embed;
use CortexPE\DiscordWebhookAPI\Message;
use CortexPE\DiscordWebhookAPI\Webhook;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use pocketmine\utils\TextFormat as TE;

class PkgallCommand extends Command {
    
    /**
     * PkgallCommand Constructor.
     */
    public function __construct(){
        parent::__construct('pkgall', 'Use command for auto package all');
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
				if(!$sender->hasPermission("timer.all.command")){
					$sender->sendMessage("".TE::RED."No tienes permisos para usar este comando");
					return;
				}
				if(empty($args[1])){
					$sender->sendMessage("".TE::RED."Usar: /{$label} {$args[0]} [Int: time]");
					return;
				}
				if(TimerPackages::isEnable()){
					$sender->sendMessage("".TE::RED."El evento se inició antes, ¡no puedes hacer esto!");
					return;
				}
				$time = $args[1];
				
				$time = Timer::time($time);
				
				TimerPackages::start($time);
				$sender->sendMessage("§aPPALL has been started");
				Loader::getInstance()->getServer()->broadcastMessage(TE::colorize("&7███████"));
                Loader::getInstance()->getServer()->broadcastMessage(TE::colorize("&7█&5█&5███&7█&7█"));
                Loader::getInstance()->getServer()->broadcastMessage(TE::colorize("&7█&5█&7██&5█&7██ &r&7[&g&lghostly &l&5PPALL&r&7]"));
                Loader::getInstance()->getServer()->broadcastMessage(TE::colorize("&7█&5█&5██&5█&7██ &r&5Ppall &ghas starter for: &f".Timer::Format($time)));
                Loader::getInstance()->getServer()->broadcastMessage(TE::colorize("&7█&5█&7███&7█&7█"));
                Loader::getInstance()->getServer()->broadcastMessage(TE::colorize("&7█&5█&7███&7█&7█"));
                Loader::getInstance()->getServer()->broadcastMessage(TE::colorize("&7███████"));
				$webHook = new Webhook(Loader::getInstance()->getConfig()->get('hcf.webhook'));
				$msg = new Message();
				$msg->setContent('<@&1184267398501629962>');
				$embed = new Embed();
				$embed->setTitle("PPALL has started");
				$embed->setColor(0xf9ff1a);
				$embed->setDescription("⏳Time: ".Timer::Format($time)."\nIp: nd-3.hydrahost.fun\nPort: 25576\nStore: https://ghostly.tebex.io/");

				$embed->setFooter("ghostlyHCF");
				$msg->addEmbed($embed);

				$webHook->send($msg);
			break;
            case "off":
                if (!$sender->hasPermission("timer.all.command")) {
                    $sender->sendMessage(TextFormat::colorize('&cNo tienes permisos para usar este comando'));
                    return;
                }
                if(!TimerPackages::isEnable()){
                    $sender->sendMessage(TextFormat::colorize('&cEl evento nunca se inició, ¡no puedes hacer esto!'));
                    return;
                }
                TimerPackages::stop();
            break;
        }
    }
}

?>