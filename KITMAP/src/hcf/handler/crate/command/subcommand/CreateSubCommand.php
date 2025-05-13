<?php

namespace hcf\handler\crate\command\subcommand;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use hcf\Loader;
use hcf\player\Player;
use hcf\utils\inventorie\Inventories;
use pocketmine\command\CommandSender;
use pocketmine\item\Item;
use pocketmine\utils\TextFormat;

class CreateSubCommand extends BaseSubCommand
{

    public function __construct(string $name, string $description = "", array $aliases = [])
    {
        parent::__construct($name, $description, $aliases);
    }

    protected function prepare(): void
    {
        $this->setPermission($this->getPermission());
        $this->registerArgument(0, new RawStringArgument("crateName", false));
        $this->registerArgument(1, new RawStringArgument("keyFormat", false));
        $this->registerArgument(2, new RawStringArgument("nameFormat", false));
        $this->registerArgument(3, new RawStringArgument("color", false));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
    	if (!$sender instanceof Player)
            return;
        
        if (count($args) < 3) {
            $sender->sendMessage(TextFormat::colorize('&c/crate create [string: crateName] [string: keyFormat] [string: nameFormat]'));
            return;
        }
        $crateName = $args["crateName"];
        $color = $args["color"];
        $keyFormat = $args["keyFormat"];
        $nameFormat = $args["nameFormat"];
        
        $item = $sender->getInventory()->getItemInHand();
        
        if (!$item instanceof Item) {
            $sender->sendMessage(TextFormat::colorize('&cInvalid keyId data'));
            return;
        }
        
        if (Loader::getInstance()->getHandlerManager()->getCrateManager()->getCrate($crateName) !== null) {
            $sender->sendMessage(TextFormat::colorize('&cThis crate already exists'));
            return;
        }
        $data = [
            'crateName' => $crateName,
            'key' => $item,
            'color' => $color,
            'keyFormat' => $keyFormat,
            'nameFormat' => $nameFormat
        ];
        Inventories::createCrateContent($sender, $data);
    }

    public function getPermission(): ?string
    {
        return "crate.command.create";
    }
}