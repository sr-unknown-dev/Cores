<?php

namespace hcf\arguments;

use CortexPE\Commando\args\BaseArgument;
use CortexPE\Commando\args\RawStringArgument;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\player\Player;
use pocketmine\Server;

class PlayersArgument extends BaseArgument
{

	public function __construct(string $name, bool $optional = false) {
        parent::__construct($name, $optional);
    }

	public function getNetworkType(): int {
		return AvailableCommandsPacket::ARG_TYPE_TARGET;
	}

	public function canParse(string $testString, CommandSender $sender): bool {
		return Player::isValidUserName($testString);
	}

	public function parse(string $argument, CommandSender $sender): mixed {
        $player = $sender->getServer()->getPlayerExact($argument);
        if($player === null) return Server::getInstance()->getOfflinePlayer($argument);
        return $player;
    }

	public function getTypeName(): string {
		return "player";
	}
}