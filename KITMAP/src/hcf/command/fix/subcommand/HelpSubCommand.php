<?php



namespace hcf\command\fix\subcommand;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\BaseSubCommand;
use hcf\faction\Faction;
use hcf\Loader;
use hcf\player\Player;
use pocketmine\command\CommandSender;
use pocketmine\item\VanillaItems;
use pocketmine\utils\TextFormat;

class HelpSubCommand extends BaseSubCommand
{
    public function __construct(string $name, string $description = "", array $aliases = [])
    {
        parent::__construct($name, $description, $aliases);
    }

    protected function prepare(): void
    {
        $this->setPermission($this->getPermission());
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender instanceof Player)
            return;
        $sender->sendMessage(
                TextFormat::colorize('&eFix commands') . "\n" .
                TextFormat::colorize('&7/fix all - &eFix all the items in your inventory and your armor') . "\n" .
                TextFormat::colorize('&7/fix hand [player] - &eFixes all items in a player\'s inventory and armor')
            );
    }

    public function getPermission(): ?string
    {
        return "use.player.command";
    }
}