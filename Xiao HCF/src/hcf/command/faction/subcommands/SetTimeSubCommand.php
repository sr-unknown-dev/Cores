<?php

namespace hcf\command\faction\subcommands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\BaseSubCommand;
use CortexPE\DiscordWebhookAPI\Embed;
use CortexPE\DiscordWebhookAPI\Message;
use CortexPE\DiscordWebhookAPI\Webhook;
use hcf\arguments\FactionsArgument;
use hcf\faction\Faction;
use hcf\Loader;
use hcf\player\Player;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class SetTimeSubCommand extends BaseSubCommand
{
    public function __construct(string $name, string $description = "", array $aliases = [])
    {
        parent::__construct($name, $description, $aliases);
    }

    protected function prepare(): void
    {
        $this->setPermission($this->getPermission());
        $this->registerArgument(0, new RawStringArgument("factionName", true));
        $this->registerArgument(1, new RawStringArgument("time", true));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender->hasPermission('setregentime.permission')) {
            return;
        }
        if (count($args) < 2) {
            $sender->sendMessage(TextFormat::colorize('&cUse /faction setregentime [string: name]  [int: time]'));
            return;
        }
        if (!is_numeric($args["time"])) {
            $sender->sendMessage(TextFormat::colorize('&cUse /faction setregentime [string: name]  [int: time]'));
            return;
        }

        $name = $args["factionName"];
        $time = $args["time"];

        if (Loader::getInstance()->getFactionManager()->getFaction($name) === null) {
            $sender->sendMessage(TextFormat::colorize('&cThere is no faction you\'re trying to change the dtr'));
            return;
        }
        Loader::getInstance()->getFactionManager()->getFaction($name)->setTimeRegeneration($time * 60);
        $sender->sendMessage(TextFormat::colorize("&8[&bAdmin&8] &a" . $name . ' faction regen time is now ' . $time . " minutes"));
        $webHook = new Webhook(Loader::getInstance()->getConfig()->get('admin.webhook'));
                $msg = new Message();
        
                $embed = new Embed();
                $embed->setTitle("New regen time 🗑️");
                $embed->setColor(0xf9ff1a);
                $embed->addField("Faction 🏠", "{$name}");
                $embed->addField("Time ⏳", "{$time}");
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