<?php

namespace hcf\command;

use hcf\faction\command\FactionSubCommand;
use hcf\Loader;
use hcf\player\Player;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class RallyCommand extends Command
{
    public function __construct()
    {
        parent::__construct("rally", "Muestra tu ubicacion a tu faction", "/rally");
        $this->setPermission("use.player.command");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (!$sender instanceof Player)
            return;

        if ($sender->getSession()->getFaction() === null) {
            $sender->sendMessage(TextFormat::colorize('&cYou don\'t have a faction'));
            return;
        }
        $faction = Loader::getInstance()->getFactionManager()->getFaction($sender->getSession()->getFaction());
        $faction->setRally([$sender->getName(), $sender->getPosition()]);
        $sender->sendMessage(TextFormat::colorize('&aNow your faction already knows your coordinates'));
    }
}