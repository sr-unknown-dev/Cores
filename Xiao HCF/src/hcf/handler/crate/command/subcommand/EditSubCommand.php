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

class EditSubCommand extends BaseSubCommand
{

    public function __construct(string $name, string $description = "", array $aliases = [])
    {
        parent::__construct($name, $description, $aliases);
    }

    protected function prepare(): void
    {
        $this->setPermission($this->getPermission());
        $this->registerArgument(0, new RawStringArgument("crateName"));
        $this->registerArgument(1, new RawStringArgument("keyId:keyFormat:nameFormat:items:color"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
    	if (!$sender instanceof Player)
            return;
            
        if (count($args) < 2) {
            $sender->sendMessage(TextFormat::colorize('&cUse /crate edit [string: crateName] [string: keyId:keyFormat:nameFormat:items:color]'));
            return;
        }
        
        if (count($args) < 0) {
            return;
        }
        $crateName = $args["crateName"];
        $type = $args["keyId:keyFormat:nameFormat:items:color"];
        
        $crate = Loader::getInstance()->getHandlerManager()->getCrateManager()->getCrate($crateName);
        
        if ($crate === null) {
            $sender->sendMessage(TextFormat::colorize('&cThis crate does not exist'));
            return;
        }
        
        switch($type) {
            case 'key':
                $item = $sender->getInventory()->getItemInHand();
        
                if (!$item instanceof Item) {
                    $sender->sendMessage(TextFormat::colorize('&cInvalid keyId data'));
                    return;
                }
                $crate->setKeyId($item);
                $sender->sendMessage(TextFormat::colorize('&akey of the crate ' . $crate->getName() . ' has been modified successfully'));
                break;
                
            case 'keyFormat':
                if (count($args) < 3) {
                    $sender->sendMessage(TextFormat::colorize('&cUse /crate edit ' . $crateName . ' keyFormat [string: format]'));
                    return;
                }
                $keyFormat = $args[2];
                
                $crate->setKeyFormat($keyFormat);
                $sender->sendMessage(TextFormat::colorize('&akeyFormat of the crate ' . $crate->getName() . ' has been modified successfully'));
                break;
                
            case 'nameFormat':
                if (count($args) < 3) {
                    $sender->sendMessage(TextFormat::colorize('&cUse /crate edit ' . $crateName . ' nameFormat [string: format]'));
                    return;
                }
                $nameFormat = $args[2];
                
                $crate->setNameFormat($nameFormat);
                $sender->sendMessage(TextFormat::colorize('&anameFormat of the crate ' . $crate->getName() . ' has been modified successfully'));
                break;
                
            case 'items':
                Inventories::editCrateContent($sender, $crateName);
                break;
            case 'color':
                if (count($args) < 3) {
                    $sender->sendMessage(TextFormat::colorize('&cUse /crate edit ' . $crateName . ' color [string: color(&|§)]'));
                    return;
                }
                $color = $args[2];
                
                $crate->setColor($color);
                $sender->sendMessage(TextFormat::colorize('&aColor of the crate ' . $crate->getName() . ' has been modified successfully'));
            	break;
                
            default:
                $sender->sendMessage(TextFormat::colorize('&cUse /crate edit ' . $crateName . ' [string: keyId:keyFormat:nameFormat:items:color]'));
                break;
        }
    }

    public function getPermission(): ?string
    {
        return "crate.command.edit";
    }
}