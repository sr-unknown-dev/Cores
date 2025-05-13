<?php

namespace hcf\command\faction\subcommands;

use CortexPE\Commando\BaseSubCommand;
use CortexPE\DiscordWebhookAPI\Embed;
use CortexPE\DiscordWebhookAPI\Message;
use CortexPE\DiscordWebhookAPI\Webhook;
use hcf\anticheat\RemoveAllDataFactions;
use hcf\Loader;
use pocketmine\command\CommandSender;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

class DisbandAllSubCommand extends BaseSubCommand
{
    private $data;

    public function __construct(string $name, string $description = "", array $aliases = [])
    {
        parent::__construct($name, $description, $aliases);
        $this->data = new Config(Loader::getInstance()->getDataFolder() . "factions.json", Config::YAML, []);
    }

    protected function prepare(): void
    {
        $this->setPermission($this->getPermission());
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender->hasPermission('forcedisband.permission')) {
            return;
        }
        
        foreach (Loader::getInstance()->getFactionManager()->getFactions() as $factions) {
            $factions->disband();
        }
        $sender->sendMessage(TextFormat::colorize('&8[&bAdmin&8] &aThe all faction was disbanded'));
        $this->data->setAll([]);
        $this->data->save();
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

    public function getPermission(): ?string
    {
        return "moderador.command";
    }
}