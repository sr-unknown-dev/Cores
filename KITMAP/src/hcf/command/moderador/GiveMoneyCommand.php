<?php

declare(strict_types=1);

namespace hcf\command\moderador;

use hcf\player\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat as TE;
use pocketmine\Server;
use hcf\Loader;

class GiveMoneyCommand extends Command
{

    public function __construct()
    {
        parent::__construct('addmoney', 'give money');
        $this->setPermission("moderador.command");
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$sender instanceof Player) return;
        
        if (!$this->testPermission($sender))
            return;
           
if (count($args) === 0) {
            $sender->sendMessage(TE::RED . "/addmoney (int|number)");
            return;
        }
if (count($args) === 1) {
            $sender->sendMessage(TE::colorize("&l&eYou've received &d".$args[0])); $sender->getSession()->setBalance($sender->getSession()->getBalance() + $args[0]);
return;
}
    }
}