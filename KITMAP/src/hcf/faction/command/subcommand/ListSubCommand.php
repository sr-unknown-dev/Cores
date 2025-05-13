<?php

namespace hcf\faction\command\subcommand;

use hcf\faction\Faction;
use hcf\Loader;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use hcf\faction\command\FactionSubCommand;

class ListSubCommand implements FactionSubCommand
{

    const FACTIONS_PER_PAGE = 10;

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     *
     * @throws TranslationException
     */
    public function execute(CommandSender $sender, array $args): void {
        $factions = array_filter(
            Loader::getInstance()->getFactionManager()->getFactions(),
            function (Faction $faction): bool { return $faction->getOnlineMembers() >= 1; }
        );
        $maxPage = ceil(count($factions) / self::FACTIONS_PER_PAGE);
        $pageStr = $args[1] ?? 1;
        $page = (int)$pageStr;

        if(!is_numeric($pageStr) || $page > $maxPage) {
            $sender->sendMessage(TextFormat::RED."Numero invalido");
            return;
        }



        usort(
            $factions,
            function (Faction $fac1, Faction $fac2) {
                // sort descending
                return -(count($fac1->getOnlineMembers()) <=> count($fac2->getOnlineMembers()));
            }
        );

        $sender->sendMessage(TextFormat::DARK_AQUA . TextFormat::BOLD . "§6Faction List §r§7(§6" . $page . "§7/§6" . $maxPage . "§7)");
        $sender->sendMessage(TextFormat::DARK_AQUA . TextFormat::BOLD . "§l§7");
        foreach (array_slice($factions, 10*($page-1), 10, true) as $index => $faction) {
            /** @var Faction $faction */
            $place = $index + 1;
            $sender->sendMessage(TextFormat::BOLD . TextFormat::GREEN . "§r§7$place. §r§l" . TextFormat::YELLOW . $faction->getName() . "§r §7(§a" . count($faction->getOnlineMembers()) . "§f/§a" . count($faction->getMembers()) . "§7)" . TextFormat::YELLOW . " §f| §l§6DTR§r§7: §6" . $faction->getDtr() . "§f/§a" . $faction->getMaxDTR());
        }
        $sender->sendMessage("\n\n§7To view more factions, do §d/f list (page)§7.");
        $sender->sendMessage("§l§7");
    }
}
