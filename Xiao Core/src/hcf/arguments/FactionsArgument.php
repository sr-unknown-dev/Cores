<?php

namespace hcf\arguments;

use CortexPE\Commando\args\RawStringArgument;
use hcf\faction\Faction;
use hcf\Loader;
use pocketmine\command\CommandSender;

final class FactionsArgument extends RawStringArgument
{
    public function canParse(string $testString, CommandSender $sender) : bool {
		return $this->getValue($testString) instanceof Faction;
	}

	public function parse(string $argument, CommandSender $sender): string
	{
		$faction = $this->getValue($argument);
		return $faction instanceof Faction ? $faction->getName() : "";
	}

	public function getValue(string $string) : ?Faction {
		return Loader::getInstance()->getFactionManager()->getFaction($string);
	}

	public function getEnumValues() : array {
		return array_keys(Loader::getInstance()->getFactionManager()->getAll());
	}

    public function getTypeName(): string {
        return "factionName";
    }
}
