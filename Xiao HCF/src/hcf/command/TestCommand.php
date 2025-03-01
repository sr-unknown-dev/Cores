<?php

namespace hcf\command;

use hcf\Factory;
use hcf\Loader;
use hcf\player\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\utils\TextFormat;

class TestCommand extends Command
{

    public function __construct()
    {
        parent::__construct("ban", "Crea un npc", "/ban");
        $this->setPermission("");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (!$sender instanceof  Player)
            return;

        if ($sender instanceof Player){
            $npc = TrapsEntity::create($sender);
            $npc->spawnToAll();
        }
    }
}