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

class AddStrikesSubCommand extends BaseSubCommand
{
    public function __construct(string $name, string $description = "", array $aliases = [])
    {
        parent::__construct($name, $description, $aliases);
    }

    protected function prepare(): void
    {
        $this->setPermission($this->getPermission());
        $this->registerArgument(0, new RawStringArgument("factionName", false));
        $this->registerArgument(1, new RawStringArgument("reason", false));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender->hasPermission('moderador.command')) {
            return;
        }
		if (count($args) < 2) {
			$sender->sendMessage(TextFormat::colorize('&cUse /faction addstrike [faction] [reason]'));
			return;
		}
		$faction = $args["factionName"];
        $reason = $args["reason"];

		if ($faction === null) {
			$sender->sendMessage(TextFormat::colorize('&cFaction not exists.'));
			return;
		}
		array_shift($args);
        $factionInstance = Loader::getInstance()->getFactionManager()->getFaction($faction);

        $currentStrikes = $factionInstance->getStrikes();

        $newStrikes = $currentStrikes + 1;

        $factionInstance->setStrikes($newStrikes);
		$sender->sendMessage(TextFormat::colorize('&8[&bAdmin&8] &aYou have added strike to ' . $faction . ' faction.'));
        $sender->sendMessage(TextFormat::colorize("&a30% of the faction points were removed"));
		$webHook = new Webhook("https://discord.com/api/webhooks/1249577658723860500/HSPJVsflett3iXoTC257Rhvv6RLN5z86shLj608xi4U_XKDaw7cekh7gcQpHLArWS1PO");
        $msg = new Message();

        $embed = new Embed();
        $embed->setTitle("New faction stike");
        $embed->setColor(0xf9ff1a);
        $embed->addField("Faction: ", $faction);
        $embed->addField("Points removed 30%:", $faction->getPoints());
        $embed->addField("Staff 👮", $sender->getName());
        $embed->setFooter("Stikes");
        $msg->addEmbed($embed);

        $webHook->send($msg);
    }

    public function getPermission(): ?string
    {
        return "moderador.command";
    }
}