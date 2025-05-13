<?php

namespace hcf\command\fix\subcommand;

use CortexPE\Commando\BaseSubCommand;
use hcf\arguments\PlayersArgument;
use hcf\player\Player;
use pocketmine\command\CommandSender;
use pocketmine\item\Armor;
use pocketmine\item\Durable;
use pocketmine\utils\TextFormat;

class HandSubCommand extends BaseSubCommand
{

    public function __construct(string $name, string $description = "", array $aliases = [])
    {
        parent::__construct($name, $description, $aliases);
    }

    protected function prepare(): void
    {
        $this->setPermission($this->getPermission());
        $this->registerArgument(0, new PlayersArgument("player"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
    	
    	if (!$sender->hasPermission('fix.player.command')) {
    		$sender->sendMessage(TextFormat::colorize('&cYou do not have permission to use this command'));
    		return;
    	}
    	
    	$player = $args["player"];
    	
    	if (!$player instanceof Player) {
    		$sender->sendMessage(TextFormat::colorize('&cThe player is not online'));
    		return;
    	}
    	
    	foreach ($player->getInventory()->getContents() as $slot => $item) {
    		if ($item instanceof Durable && $item->getDamage() > 0) {
    			$newItem = $item->setDamage(0);     
    			$player->getInventory()->setItem($slot, $newItem);
    		}
    	}
    	
    	foreach ($player->getArmorInventory()->getContents() as $slot => $armor) {
    		if(!$armor instanceof Armor) return;
    		if ($armor->getDamage() > 0) {
    			$newArmor = $armor->setDamage(0); $player->getArmorInventory()->setItem($slot, $newArmor);
    		}
    	}
    	$sender->sendMessage(TextFormat::colorize('&aYou have fixed the items and the armor to the player &g' . $player->getName()));
    	$player->sendMessage(TextFormat::colorize('&aSomeone fixed your items and armor successfully for &g'.$sender->getName()));
    }

    public function getPermission(): ?string
    {
        return "fix.player.command";
    }
}