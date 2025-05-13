<?php

namespace hcf\arguments;

use CortexPE\Commando\args\TextArgument;
use hcf\faction\Faction;
use hcf\Loader;
use hcf\player\Player;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\player\Player as PlayerPlayer;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class MsgArgument extends TextArgument
{

	public function parse(string $argument, CommandSender $sender): string
    {
        return $argument;
    }
}