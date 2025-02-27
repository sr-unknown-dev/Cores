<?php

namespace hcf\arguments;

use CortexPE\Commando\args\StringEnumArgument;
use hcf\Loader;
use pocketmine\command\CommandSender;

class CratesArgument extends StringEnumArgument
{

    /**
     * @inheritDoc
     */
    public function parse(string $argument, CommandSender $sender): mixed
    {
        return $argument;
    }

    public function getValue(string $string)
    {
        return $this->getEnumValues()[$string];
    }

    public function getEnumValues(): array
    {
        $crates = Loader::getInstance()->getHandlerManager()->getCrateManager()->getCrates();
        return array_keys($crates);
    }

    public function getTypeName(): string
    {
        return "crateName";
    }
}