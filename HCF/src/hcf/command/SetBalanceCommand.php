<?php

declare(strict_types=1);

namespace hcf\command;

use hcf\player\Player;
use hcf\Loader;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class SetBalanceCommand extends Command
{

    public function __construct()
    {
        parent::__construct('setbalance', 'Use command for setbalance');
        $this->setPermission("coins.set.command");
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$sender instanceof Player) {
            return;
        }
        
        if (count($args) < 2) {
            $sender->sendMessage(TextFormat::colorize("&cUso: /setbalance {player} {coins}"));
            return;
        }
        
        $targetName = $args[0];
        $targetPlayer = Loader::getInstance()->getServer()->getPlayerExact($targetName);
        
        if (!$targetPlayer instanceof Player) {
            $sender->sendMessage(TextFormat::RED."Jugador $targetPlayer no encontrado!");
            return;  
        }
        
        $coins = (int)$args[1];
        $targetPlayer->getSession()->setBalance($coins);

        if ($coins === null) {
            $sender->sendMessage(TextFormat::RED."Error al setear el balance.");
        }
        
        $sender->sendMessage(TextFormat::colorize("&eBalance of &f". $targetName . " &eset &a$". $targetPlayer->getSession()->getBalance()));
    }
}