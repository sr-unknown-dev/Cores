<?php

namespace hcf\command\moderador;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseCommand;
use hcf\Loader;
use InvalidArgumentException;
use pocketmine\command\CommandSender;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\StringToEnchantmentParser;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class EnchantAllCommand extends BaseCommand {

    public function __construct()
    {
        parent::__construct(Loader::getInstance(), "enchantall", "Enchant all items in your inventory");
    }

    protected function prepare(): void
    {
        $this->registerArgument(0, new RawStringArgument("enchantment", true));
        $this->registerArgument(1, new RawStringArgument("level", true));
        $this->setPermission($this->getPermission());
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if(!$sender instanceof Player){
            $sender->sendMessage(TextFormat::RED . "This command can only be used in-game!");
            return;
        }

        if(!isset($args["enchantment"])){
            $sender->sendMessage(TextFormat::RED . "Usage: /enchantall <enchantment> [level]");
            return;
        }

        $enchantment = StringToEnchantmentParser::getInstance()->parse($args["enchantment"]);
        if($enchantment === null){
            $sender->sendMessage(TextFormat::RED . "Invalid enchantment name!");
            return;
        }

        $level = isset($args["level"]) ? (int)$args["level"] : 1;
        if($level < 1){
            $level = 1;
        }

        $inventory = $sender->getInventory();
        $count = 0;

        foreach($inventory->getContents() as $slot => $item){
            if($item->hasEnchantment($enchantment)){
                continue;
            }
            
            try {
                $item->addEnchantment(new EnchantmentInstance($enchantment, $level));
                $inventory->setItem($slot, $item);
                $count++;
            } catch (InvalidArgumentException $e) {
                continue;
            }
        }

        if($count > 0){
            $sender->sendMessage(TextFormat::GREEN . "Successfully enchanted " . $count . " items with " . $enchantment->getName()->getText() . " " . $level);
        } else {
            $sender->sendMessage(TextFormat::RED . "No items could be enchanted with " . $enchantment->getName()->getText());
        }
    }

    public function getPermission(): ?string
    {
        return "moderador.command";
    }
}