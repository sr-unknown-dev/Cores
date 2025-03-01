<?php

namespace hcf\command\faction\subcommands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\BaseSubCommand;
use CortexPE\DiscordWebhookAPI\Message;
use CortexPE\DiscordWebhookAPI\Webhook;
use hcf\arguments\FactionsArgument;
use hcf\faction\Faction;
use hcf\Loader;
use hcf\player\Player;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class ForceDisbandSubCommand extends BaseSubCommand
{
    public function __construct(string $name, string $description = "", array $aliases = [])
    {
        parent::__construct($name, $description, $aliases);
    }

    protected function prepare(): void
    {
        $this->setPermission($this->getPermission());
        $this->registerArgument(0, new RawStringArgument("factionName", true));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender->hasPermission('forcedisband.permission')) {
            return;
        }
        if (count($args) < 1) {
            $sender->sendMessage(TextFormat::colorize('&cUse /faction forcedisband [string: name]'));
            return;
        }

        $name = $args["factionName"];

        if (Loader::getInstance()->getFactionManager()->getFaction($name) === null) {
            $sender->sendMessage(TextFormat::colorize('&cThere is no faction you\'re trying to change the dtr'));
            return;
        }
        $faction = Loader::getInstance()->getFactionManager()->getFaction($name);
        $faction->disband();
        Loader::getInstance()->getFactionManager()->removeFaction($name);
        $sender->sendMessage(TextFormat::colorize('&aThe ' . $name .' &afaction was disbanded'));
        $webHook = new Webhook(Loader::getInstance()->getConfig()->get('admin.webhook'));
        $msg = new Message();
        $msg->setContent('The **' . $name .'** faction was disbanded by staff **' . $sender->getName() . '**');
        $webHook->send($msg);
    }

    public function getPermission(): ?string
    {
        return "use.player.command";
    }
}