<?php

declare(strict_types=1);

namespace hcf\command;

use hcf\player\Player;

use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\item\Armor;
use pocketmine\item\Tool;
use pocketmine\utils\TextFormat;

/**
 * Class RenameCommand
 * @package hcf\command
 */
class RenameCommand extends Command
{
	
    /**
     * RenameCommand construct.
     */
    public function __construct()
    {
        parent::__construct('rename', 'Command for rename');
        $this->setPermission('rename.command');
    }
	
    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$sender instanceof Player)
            return;
            
        if (!$this->testPermission($sender))
            return;
        
        if (count($args) < 1) {
            $sender->sendMessage(TextFormat::colorize('&cUse: /rename <name>'));
            return;
        }

        $name = implode(' ', $args);
        
        if (strtolower(end($args)) === "all") {
            $name = implode(' ', array_slice($args, 0, -1));
            $count = 0;
            
            foreach ($sender->getInventory()->getContents() as $slot => $item) {
                if ($item instanceof Tool || $item instanceof Armor) {
                    $newItem = clone $item;
                    $newItem->setCustomName(TextFormat::colorize($name));
                    $sender->getInventory()->setItem($slot, $newItem);
                    $count++;
                }
            }
            
            if ($count > 0) {
                $sender->sendMessage(TextFormat::colorize("&aSuccessfully renamed {$count} items"));
            } else {
                $sender->sendMessage(TextFormat::colorize('&cNo valid items found to rename'));
            }
            return;
        }

        $item = $sender->getInventory()->getItemInHand();
        if (!$item instanceof Tool && !$item instanceof Armor) {
            $sender->sendMessage(TextFormat::colorize('&cYou have no armor and no tools in your hand'));
            return;
        }

        $newItem = clone $item;
        $newItem->setCustomName(TextFormat::colorize($name));
        $sender->getInventory()->setItemInHand($newItem);
        $sender->sendMessage(TextFormat::colorize('&aYou have successfully renamed the item'));
    }
}