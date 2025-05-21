<?php

declare(strict_types=1);

namespace hcf\command\moderador\kitsnpc;

use hcf\entity\kits\KitNPC;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use hcf\player\Player;
use pocketmine\item\VanillaItems;

class KitNPCCommand extends Command {

    public function __construct() {
        parent::__construct("kitmap", "Create and manage kit NPCs", "/kitmap <create|tool> <display_text> <kit_name>", ["kitnpc"]);
        $this->setPermission("kitnpc.cmd");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        if(!$sender instanceof Player) {
            $sender->sendMessage("§cThis command can only be used in-game!");
            return false;
        }

        if(!isset($args[0])) {
            $sender->sendMessage("§cUsage: /kitmap <create|tool> <display_text> <kit_name>");
            return false;
        }

        switch($args[0]) {
            case "create":
                if(!isset($args[1]) || !isset($args[2])) {
                    $sender->sendMessage("§cUsage: /kitmap create <display_text> <kit_name>");
                    return false;
                }

                $displayText = $args[1];
                $kitName = $args[2];

                $npc = KitNPC::create($sender, $displayText, $kitName);
                $npc->spawnToAll();

                $sender->sendMessage("§aSuccessfully created kit NPC!");
                return true;

            case "tool":
                if(!$sender->hasPermission("kitnpc.cmd")) {
                    $sender->sendMessage("§cYou don't have permission to use this command!");
                    return false;
                }

                $tool = VanillaItems::GOLDEN_HOE();
                $tool->setCustomName("§eRemove NPC §r§7(Right Click)");
                $sender->getInventory()->addItem($tool);
                $sender->sendMessage("§aYou received the NPC removal tool!");
                return true;

            default:
                $sender->sendMessage("§cUsage: /kitmap <create|tool> <display_text> <kit_name>");
                return false;
        }
    }
}