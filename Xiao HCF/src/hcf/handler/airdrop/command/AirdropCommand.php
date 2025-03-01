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

namespace hcf\handler\airdrop\command;

use CortexPE\Commando\BaseCommand;
use hcf\handler\airdrop\command\subcommands\EditSubCommand;
use hcf\handler\airdrop\command\subcommands\GiveAllSubCommand;
use hcf\handler\airdrop\command\subcommands\GiveSubCommand;
use hcf\Loader;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class AirdropCommand extends BaseCommand
{
    public function __construct(string $name, string $description = "")
    {
        parent::__construct(
            Loader::getInstance(),
            $name,
            $description
        );
    }

    protected function prepare(): void
    {
        $this->setPermission($this->getPermission());
        $this->registerSubCommand(new EditSubCommand("edit", "para editar el contenido de los airdrops"));
        $this->registerSubCommand(new GiveAllSubCommand("giveall", "para darle airdrops a todos los que esten onlin"));
        $this->registerSubCommand(new GiveSubCommand("give", "para darte airdrops"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        $sender->sendMessage(TextFormat::GREEN."Use: /airdrop (giveall|give|edit)");
    }

    public function getPermission()
    {
        return "airdrop.commands";
    }
}
?>