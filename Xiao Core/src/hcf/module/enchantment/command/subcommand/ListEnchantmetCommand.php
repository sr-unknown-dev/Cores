<?php

declare(strict_types=1);

namespace hcf\module\enchantment\command\subcommand;

use hcf\module\enchantment\command\EnchantmentSubCommand;
use hcf\Loader;
use hcf\player\Player;

use pocketmine\command\CommandSender;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\utils\TextFormat;

/**
 * Class AddSubCommand
 * @package hcf\module\enchantment\command\subcommand
 */
class ListEnchantmetCommand implements EnchantmentSubCommand
{
    
    /**
     * @param CommandSender $sender
     * @param array $args
     */
    public function execute(CommandSender $sender, array $args): void
    {
        if (!$sender instanceof Player)
            return;

        
        $sender->sendMessage(TextFormat::colorize("&fCustom Enchantments ID"));
        $sender->sendMessage(TextFormat::colorize("&7---------------------------------"));
        $sender->sendMessage(TextFormat::colorize("&cSPEED&7: 37"));
        $sender->sendMessage(TextFormat::colorize("&cINVISIBILITY&7: 38"));
        $sender->sendMessage(TextFormat::colorize("&cNIGTH_VISION&7: 39"));
        $sender->sendMessage(TextFormat::colorize("&cFIRE_RESISTANCE&7: 40"));
        $sender->sendMessage(TextFormat::colorize("&cIMPLANTS&7: 41"));
        $sender->sendMessage(TextFormat::colorize("&cHELLFORGED&7: 42"));
        $sender->sendMessage(TextFormat::colorize("&7---------------------------------"));
    }
}