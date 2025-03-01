<?php

namespace hcf\command\events\subcommands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\BaseSubCommand;
use hcf\command\pay\args\AmountArgument;
use hcf\command\pay\args\PlayersOnline;
use hcf\faction\Faction;
use hcf\Loader;
use hcf\player\Player;
use hcf\Server\ClaimSe;
use hcf\utils\inventorie\Inventories;
use hcf\utils\time\Timer;
use pocketmine\command\CommandSender;
use pocketmine\item\VanillaItems;
use pocketmine\utils\TextFormat;

class HcfSubCommand extends BaseSubCommand
{

    public function __construct(string $name, string $description = "", array $aliases = [])
    {
        parent::__construct($name, $description, $aliases);
    }

    protected function prepare(): void
    {
        $this->setPermission($this->getPermission());
        $this->registerArgument(0, new RawStringArgument("time"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender instanceof Player) return;

    	if ($sender instanceof Player) {
            
            $time = $args["time"];
            $time = Timer::time($time);
            Inventories::HCFEvents($sender, $time);
        }
    }

    public function getPermission(): ?string
    {
        return "moderador.command";
    }
}