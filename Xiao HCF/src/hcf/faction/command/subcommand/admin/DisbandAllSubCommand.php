<?php

declare(strict_types=1);

namespace hcf\faction\command\subcommand\admin;

use CortexPE\DiscordWebhookAPI\Embed;
use CortexPE\DiscordWebhookAPI\Message;
use CortexPE\DiscordWebhookAPI\Webhook;
use hcf\faction\command\FactionSubCommand;
use hcf\Loader;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class DisbandAllSubCommand implements FactionSubCommand
{

    /**
     * @param CommandSender $sender
     * @param array $args
     */
    public function execute(CommandSender $sender, array $args): void
    {
        if (!$sender->hasPermission('forcedisband.permission')) {
            return;
        }
        
        foreach (Loader::getInstance()->getFactionManager()->getFactions() as $factions) {
            $factions->disband();
        }
        /*$faction = Loader::getInstance()->getFactionManager()->getFaction($name);
        $faction->disband();
        Loader::getInstance()->getFactionManager()->removeFaction($name);*/
		//$name = "";
        $sender->sendMessage(TextFormat::colorize('&8[&bAdmin&8] &aThe all faction was disbanded'));
        $webHook = new Webhook(Loader::getInstance()->getConfig()->get('admin.webhook'));
        $msg = new Message();

        $embed = new Embed();
        $embed->setTitle("New faction disband 🗑️");
        $embed->setColor(0xf9ff1a);
        $embed->addField("Faction 🏠", "All");
        $embed->addField("Staff 👮", $sender->getName());
        $embed->setFooter("Administration");
        $msg->addEmbed($embed);

        $webHook->send($msg);
    }
}
