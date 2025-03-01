<?php

declare(strict_types=1);

namespace hcf\prefix\command;

use pocketmine\command\CommandSender;

interface PrefixSubCommand
{
    
    /**
     * @param CommandSender $sender
     * @param array $args
     */
    public function execute(CommandSender $sender, array $args): void;
}