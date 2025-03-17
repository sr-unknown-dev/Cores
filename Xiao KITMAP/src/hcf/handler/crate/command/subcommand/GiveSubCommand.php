<?php

namespace hcf\handler\crate\command\subcommand;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\BaseSubCommand;
use hcf\arguments\CratesArgument;
use hcf\faction\Faction;
use hcf\Loader;
use hcf\player\Player;
use pocketmine\block\VanillaBlocks;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class GiveSubCommand extends BaseSubCommand
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
            
        if (count($args) === 0) {
            $sender->sendMessage(TextFormat::colorize('&cUse /crate give [string: crateName]'));
            return;
        }
        $crateName = $args["crateName"];
        $crate = Loader::getInstance()->getHandlerManager()->getCrateManager()->getCrate($crateName);
        
        if ($crate === null) {
            $sender->sendMessage(TextFormat::colorize('&cThis crate does not exist'));
            return;
        }
        $chest = VanillaBlocks::CHEST()->asItem();
        $chest->setCustomName(TextFormat::colorize('Crate ' . $crateName));
        
        $namedtag = $chest->getNamedTag();
        $namedtag->setString('crate_place', $crateName);
        $chest->setNamedTag($namedtag);
            
        $sender->sendMessage(TextFormat::colorize('&aCrate ' . $crateName . ' given'));
        $sender->getInventory()->addItem($chest);
    }

    public function getPermission(): ?string
    {
        return "crate.command.give";
    }
}