<?php

namespace hcf\module\treasureisland\command;

use hcf\Loader;
use hcf\player\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class TreasureCommand extends Command {

    public function __construct()
    {
        parent::__construct('treasure', 'treasure island commands');
        $this->setPermission('pkg.command');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (count($args) === 0) {
            $sender->sendMessage(
                TextFormat::GRAY . ("----------------------------------------------------------\n") .
                TextFormat::colorize("\n") .
                TextFormat::YELLOW . "/treasure - " . TextFormat::WHITE . ("use this command to get plugin information\n") .
                TextFormat::YELLOW . "/treasure update - " . TextFormat::WHITE . ("use this command to update island\n") .
                TextFormat::YELLOW . "/treasure editcontent -" . TextFormat::WHITE . ("use this command to edit the chest content\n") .
                TextFormat::colorize("\n") .
                TextFormat::GRAY . ("----------------------------------------------------------\n")
            );
            return;
        }
        
        switch ($args[0]) {
            case "update":
                if (!$sender->getServer()->isOp($sender->getName())) {
                    $sender->sendMessage(TextFormat::RED . "You don't have permissions");
                    return;
                }
                
                if (!$sender instanceof Player) {
                    $sender->sendMessage(TextFormat::RED . "This message can only be executed in game!");
                    return;
                }
                Loader::getInstance()->getModuleManager()->getTreasureIslandManager()->update();
                break;
            case "editcontent":
                if (!$sender->getServer()->isOp($sender->getName())) {
                    $sender->sendMessage(TextFormat::RED . "You don't have permissions");
                    return;
                }
                
                if (!$sender instanceof Player) {
                    $sender->sendMessage(TextFormat::RED . "This message can only be executed in game!");
                    return;
                }
                $player = Loader::getInstance()->getServer()->getPlayerExact($sender->getName());
                Loader::getInstance()->getModuleManager()->getTreasureIslandManager()->setItems($player->getInventory()->getContents());
                $sender->sendMessage(TextFormat::GREEN . "The content has been edited correctly");
                break;
            }
    }
    
}