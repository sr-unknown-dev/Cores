<?php

namespace hcf\timer\command\FreeKitsSubCommands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use CortexPE\DiscordWebhookAPI\Embed;
use CortexPE\DiscordWebhookAPI\Message;
use CortexPE\DiscordWebhookAPI\Webhook;
use hcf\Loader;
use hcf\player\Player;
use hcf\timer\types\TimerFreeKits;
use hcf\utils\time\Timer;
use pocketmine\command\CommandSender;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class EnableSubCommand extends BaseSubCommand {

    public function __construct() {
        parent::__construct("enable", "Enable the timer", []);
    }

    protected function prepare(): void {
        $this->registerArgument(0, new RawStringArgument("time", true));
        $this->setPermission("moderador.command");
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
        if (!$sender instanceof Player) {
            $sender->sendMessage("This command can only be used in-game.");
            return;
        }

        if(empty($args["time"])){
            $sender->sendMessage(" ".TextFormat::RED."Usar: /{$aliasUsed} {$args["on|off"]} [Int: time]");
            return;
        }
        if(TimerFreeKits::isEnable()){
            $sender->sendMessage(" ".TextFormat::RED."El evento se inició antes, ¡no puedes hacer esto!");
            return;
        }

        $time = $args["time"];
        
        $time = Timer::time($time);
        
        TimerFreeKits::start($time);
        foreach (Server::getInstance()->getOnlinePlayers() as $players) {
            $players->sendMessage(TextFormat::colorize("&l&aFreeKits has started\n&l&c¡Pueden obtener kits gratis!"));
        }
        $webHook = new Webhook(Loader::getInstance()->getConfig()->get('hcf.webhook'));
        $msg = new Message();
        $msg->setContent('<@&1184267398501629962>');
        $embed = new Embed();
        $embed->setTitle("FreeKits has started");
        $embed->setColor(0xf9ff1a);
        $embed->setDescription("⏳Time: ".Timer::Format($time)."\nIp: Xiaohcf.ddns.net\nPort: 25576\nStore: https://Xiao.tebex.io/");
 
        $embed->setFooter("XiaoHCF");
        $msg->addEmbed($embed);
        
        $webHook->send($msg);
    }
}