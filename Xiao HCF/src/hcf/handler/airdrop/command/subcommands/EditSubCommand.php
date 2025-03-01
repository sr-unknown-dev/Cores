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

use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\BaseSubCommand;
use hcf\handler\airdrop\AirdropManager;
use hcf\Loader;
use hcf\player\Player;
use pocketmine\command\CommandSender;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

class EditSubCommand extends BaseSubCommand
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
        if (!$sender instanceof Player)
        return;

        if ($sender instanceof Player) {
            /*$items = $sender->getInventory()->getContents();
            AirdropManager::getAirdrop()->setItems($items);*/
            AirdropManager::getAirdrop()->sendMenu($sender);
            $sender->sendMessage("§aAirdrop content update");
        }
    }

    public function getPermission(): ?string
    {
        return "airdrop.edit";
    }
}
?>