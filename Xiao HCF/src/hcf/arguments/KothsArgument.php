<?php

namespace hcf\arguments;

use CortexPE\Commando\args\StringEnumArgument;
use hcf\Loader;
use hcf\koth\Koth;
use pocketmine\command\CommandSender;

class KothsArgument extends StringEnumArgument
{
    public function canParse(string $testString, CommandSender $sender): bool {
        return $this->getValue($testString) instanceof Koth;
    }

    public function parse(string $argument, CommandSender $sender): mixed
    {
        return $argument;
    }

    public function getEnumValues(): array
    {
        $koths = Loader::getInstance()->getKothManager()->getKoths();
        return array_keys($koths);
    }

    public function getTypeName(): string
    {
        return "kothName";
    }
}