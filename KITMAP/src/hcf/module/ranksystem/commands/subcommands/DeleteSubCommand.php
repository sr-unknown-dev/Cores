<?php

namespace hcf\module\ranksystem\commands\subcommands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use hcf\Loader;
use hcf\module\ranksystem\forms\RankDeleteConfirmForm;
use hcf\player\Player;
use pocketmine\command\CommandSender;

class DeleteSubCommand extends BaseSubCommand {

    public function __construct(string $name, string $description = "")
    {
        parent::__construct($name, $description);
    }

    protected function prepare(): void {
        $this->setPermission($this->getPermission());
        $this->registerArgument(0, new RawStringArgument("name", false));
    }

    public function onRun(CommandSender $sender, string $label, array $args): void {

        if ($sender instanceof Player) {
            $sender->sendForm(new RankDeleteConfirmForm(Loader::getInstance()->getRankManager(), $sender, $args["name"]));
        
        }
    }

    public function getPermission(): ?string
    {
        return "ranks.commands";
    }
}
