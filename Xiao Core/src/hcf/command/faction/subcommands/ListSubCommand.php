<?php

namespace hcf\command\faction\subcommands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\exception\ArgumentOrderException;
use hcf\faction\Faction;
use hcf\Loader;
use hcf\player\Player;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class ListSubCommand extends BaseSubCommand
{

    const FACTIONS_PER_PAGE = 10;

    /**
     * @param string $name
     * @param string $description
     * @param array $aliases
     */
    public function __construct(string $name, string $description = "", array $aliases = [])
    {
        parent::__construct($name, $description, $aliases);
    }

    /**
     * @return void
     * @throws ArgumentOrderException
     */
    protected function prepare(): void
    {
        $this->setPermission($this->getPermission());
        $this->registerArgument(0, new RawStringArgument("page", true));
    }

    /**
     * @param CommandSender $sender
     * @param string $aliasUsed
     * @param array $args
     * @return void
     */
    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        $factions = array_filter(

            Loader::getInstance()->getFactionManager()->getFactions(),

            function (Faction $faction): bool { return $faction->getOnlineMembers() >= 1; }
        );
        $maxPage = ceil(count($factions) / self::FACTIONS_PER_PAGE);
        $pageStr = $args["page"] ?? 1;
        $page = (int)$pageStr;

        if(!is_numeric($pageStr) || $page > $maxPage) {
            $sender->sendMessage(TextFormat::RED."Numero invalido");
            return;
        }



        usort(
            $factions,
            function (Faction $fac1, Faction $fac2) {
                return -(count($fac1->getOnlineMembers()) <=> count($fac2->getOnlineMembers()));
            }
        );

        $sender->sendMessage(TextFormat::DARK_AQUA . TextFormat::BOLD . "§4Faction List §r§7(§4" . $page . "§7/§4" . $maxPage . "§7)");
        $sender->sendMessage(TextFormat::DARK_AQUA . TextFormat::BOLD . "§l§7");
        foreach (array_slice($factions, 10*($page-1), 10, true) as $index => $faction) {
            /** @var Faction $faction */
            $place = $index + 1;
            $sender->sendMessage(TextFormat::BOLD . TextFormat::GREEN . "§r§7$place. §r§l" . TextFormat::YELLOW . $faction->getName() . "§r §7(§a" . count($faction->getOnlineMembers()) . "§f/§a" . count($faction->getMembers()) . "§7)" . TextFormat::YELLOW . " §f| §l§6DTR§r§7: §6" . $faction->getDtr() . "§f/§a" . $faction->getMaxDTR());
        }
        $sender->sendMessage("\n\n§7To view more factions, do §d/f list (page)§7.");
        $sender->sendMessage("§l§7");
    }

    /**
     * @return string|null
     */
    public function getPermission(): ?string
    {
        return "use.player.command";
    }
}