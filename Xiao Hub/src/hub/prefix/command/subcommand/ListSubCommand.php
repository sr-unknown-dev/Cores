<?php

declare(strict_types=1);

namespace hub\prefix\command\subcommand;

use hub\prefix\command\PrefixSubCommand;
use hub\prefix\utils\Utils;
use hub\prefix\entity\PrefixEntity;
use hub\prefix\Prefix;
use hub\Loader;
use hub\player\Player;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class ListSubCommand implements PrefixSubCommand
{

    /**
     * @param CommandSender $sender
     * @param array $args
     */
    public function execute(CommandSender $sender, array $args): void
    {
        if (!$sender->hasPermission("moderador.command")) {
            return;
        }
        if (!$sender instanceof Player)
            return;
      
        $page = 1;
        if(isset($args[1]) && is_numeric($args[1])) {
            $page = max(1, $args[1]); 
        }
      
        $manager = Loader::getInstance()->getPrefixManager();

        $prefixesPerPage = 10;
        $totalPrefixes = count($manager->getPrefixes());

        $maxPages = ceil($totalPrefixes / $prefixesPerPage);
        $page = max(1, min($page, $maxPages));
      
        $startIndex = ($page - 1) * $prefixesPerPage;
        $endIndex = $startIndex + $prefixesPerPage;
      
        $message = "§fPrefixes List §e(Pagina $page/$maxPages):\n";
      
        $results = array_slice($manager->getPrefixes(), $startIndex, $prefixesPerPage, true);
        foreach($results as $name => $prefix) {
            $message .= "§f$name: {$prefix->getFormat()}§r"; 
            if($prefix->getPermission() !== null) {
                $message .= " §f({$prefix->getPermission()})\n";
            } else {
                $message .= "\n";  
            }
        }
      
        $sender->sendMessage($message);
    }
}
