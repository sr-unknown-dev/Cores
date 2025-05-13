<?php

namespace hcf\command\faction\subcommands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\BaseSubCommand;
use hcf\faction\Faction;
use hcf\Loader;
use hcf\player\Player;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class TopSubCommand extends BaseSubCommand
{
    public function __construct(string $name, string $description = "", array $aliases = [])
    {
        parent::__construct($name, $description, $aliases);
    }

    protected function prepare(): void
    {
        $this->setPermission($this->getPermission());
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        $data = $this->getFactions();
        arsort($data);
        
        $sender->sendMessage(TextFormat::colorize('&3Top factions &7(points)'));
        
        for ($i = 0; $i < 10; $i++) {
            $position = $i + 1;
            $factions = array_keys($data);
            $points = array_values($data);
            
            if (isset($factions[$i]))
                $sender->sendMessage(TextFormat::colorize('&7#' . $position . '. &e' . $factions[$i] . ' &7- &f' . $points[$i]));
        }
    }

    public function getPermission(): ?string
    {
        return "use.player.command";
    }

    private function getFactions(): array
    {
        $points = [];
        
        foreach (Loader::getInstance()->getFactionManager()->getFactions() as $name => $faction) {
            if (in_array($faction->getName(), ['Spawn', 'North Road', 'South Road', 'East Road', 'West Road', 'Nether Spawn', 'End Spawn']))
                continue;
            $points[$name] = $faction->getPoints();
        }
        return $points;
    }
}