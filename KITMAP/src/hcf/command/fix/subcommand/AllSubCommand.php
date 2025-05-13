<?php

namespace hcf\command\fix\subcommand;

use CortexPE\Commando\BaseSubCommand;
use pocketmine\command\CommandSender;
use pocketmine\item\Armor;
use pocketmine\item\Durable;
use pocketmine\utils\TextFormat;

class AllSubCommand extends BaseSubCommand
{

    public function __construct(string $name, string $description = "", array $aliases = []){parent::__construct($name, $description, $aliases);}

    protected function prepare(): void{$this->setPermission($this->getPermission());}

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
    	foreach ($sender->getInventory()->getContents() as $slot => $item) {
    		
    		if ($item instanceof Durable && $item->getDamage() > 0) {
    			$newItem = $item->setDamage(0);
    			$sender->getInventory()->setItem($slot, $newItem);
    		}
    	}
    	
    	foreach ($sender->getArmorInventory()->getContents() as $slot => $armor) {
    		if(!$armor instanceof Armor) return;
    		if ($armor->getDamage() > 0) {
    			$newArmor = $armor->setDamage(0); $sender->getArmorInventory()->setItem($slot, $newArmor);
    		}
    	}
        $sender->sendMessage(TextFormat::colorize('&aSomeone fixed your items and armor successfully for '));
    }

    public function getPermission(): ?string
    {
        return "fix.player.command";
    }
}