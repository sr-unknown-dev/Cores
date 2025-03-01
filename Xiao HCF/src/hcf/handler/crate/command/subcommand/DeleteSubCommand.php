<?php

namespace hcf\handler\crate\command\subcommand;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\BaseSubCommand;
use hcf\arguments\CratesArgument;
use hcf\faction\Faction;
use hcf\Loader;
use hcf\player\Player;
use hcf\Server\ClaimSe;
use hcf\utils\inventorie\Inventories;
use pocketmine\command\CommandSender;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\utils\TextFormat;

class DeleteSubCommand extends BaseSubCommand
{

    public function __construct(string $name, string $description = "", array $aliases = [])
    {
        parent::__construct($name, $description, $aliases);
    }

    protected function prepare(): void
    {
        $this->setPermission($this->getPermission());
        $this->registerArgument(0, new RawStringArgument("crateName"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
    	if (!$sender instanceof Player)
            return;
            
        if (!isset($args["crateName"])) {
            $sender->sendMessage(TextFormat::colorize('&c/crate delete [string: crateName]'));
            return;
        }
        $crateName = $args["crateName"];
        
        if (Loader::getInstance()->getHandlerManager()->getCrateManager()->getCrate($crateName) === null) {
            $sender->sendMessage(TextFormat::colorize('&cThis crate does not exist'));
            return;
        }
        Loader::getInstance()->getHandlerManager()->getCrateManager()->removeCrate($crateName);
        $sender->sendMessage(TextFormat::colorize('&cYou have removed the crate ' . $crateName . '. Now remove the chests that have been created with this crate'));
    }

    public function getPermission(): ?string
    {
        return "crate.command.delete";
    }
}