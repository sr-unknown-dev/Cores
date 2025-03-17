<?php

/**
*     _    ___ ____  ____  ____   ___  ____     
*    / \  |_ _|  _ \|  _ \|  _ \ / _ \|  _ \    
*   / _ \  | || |_) | | | | |_) | | | | |_) |   
*  / ___ \ | ||  _ <| |_| |  _ <| |_| |  __/    
* /_/___\_\___|_| \_\____/|_| \_\\___/|_| ____  
*  / ___/ _ \|  \/  |  \/  |  / \  | \ | |  _ \ 
* | |  | | | | |\/| | |\/| | / _ \ |  \| | | | |
* | |__| |_| | |  | | |  | |/ ___ \| |\  | |_| |
*  \____\___/|_|  |_|_|  |_/_/   \_\_| \_|____/ 
 */

namespace hcf\handler\airdrop\command\subcommands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use hcf\Factory;
use hcf\handler\airdrop\Airdrop;
use hcf\player\Player;
use pocketmine\block\VanillaBlocks;
use pocketmine\command\CommandSender;
use pocketmine\item\Item;
use pocketmine\Server;

class GiveAllSubCommand extends BaseSubCommand
{
    public function __construct(string $name, string $description = "", array $aliases = [])
    {
        parent::__construct($name, $description, $aliases);
    }

    protected function prepare(): void
    {
        $this->setPermission($this->getPermission());
        $this->registerArgument(0, new RawStringArgument("count"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender instanceof Player)
            return;

        if ($sender instanceof Player) {
            foreach (Server::getInstance()->getOnlinePlayers() as $players){
                Factory::getAirdrop($players, $args["count"]);
            }
        }
    }

    public function getPermission(): ?string
    {
        return "airdrop.give";
    }
}
?>