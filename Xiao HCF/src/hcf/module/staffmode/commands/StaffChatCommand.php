<?php

namespace hcf\module\staffmode\commands;

use hcf\Loader;
use hcf\player\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class StaffChatCommand extends Command
{
    public function __construct()
    {
        parent::__construct("staff", "staffmode");
        $this->setPermission("staff.cmds");
    }

    /**
     * @inheritDoc
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (!$sender instanceof Player) return;

        if (Loader::getInstance()->getStaffModeManager()->isStaffChat($sender)) {
            Loader::getInstance()->getStaffModeManager()->toggleStaffChat($sender);
        }else{
            Loader::getInstance()->getStaffModeManager()->toggleStaffChat($sender);
        }
    }
}