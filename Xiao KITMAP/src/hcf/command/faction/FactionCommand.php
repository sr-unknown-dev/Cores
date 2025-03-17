<?php

namespace hcf\command\faction;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseCommand;
use hcf\command\faction\subcommands\AddDtrSubCommand;
use hcf\command\faction\subcommands\ClaimSubCommand;
use hcf\command\faction\subcommands\CreateSubCommand;
use hcf\command\faction\subcommands\DepositSubCommand;
use hcf\command\faction\subcommands\DisbandSubCommand;
use hcf\command\faction\subcommands\FocusSubCommand;
use hcf\command\faction\subcommands\HelpSubCommand;
use hcf\command\faction\subcommands\HomeSubCommand;
use hcf\command\faction\subcommands\InviteSubCommand;
use hcf\command\faction\subcommands\JoinSubCommand;
use hcf\command\faction\subcommands\MapSubCommand;
use hcf\command\faction\subcommands\SetHomeSubCommand;
use hcf\command\faction\subcommands\StuckSubCommand;
use hcf\command\faction\subcommands\TopSubCommand;
use hcf\command\faction\subcommands\AddPointsSubCommand;
use hcf\command\faction\subcommands\AddStrikesSubCommand;
use hcf\command\faction\subcommands\CampSubCommand;
use hcf\command\faction\subcommands\ChatSubCommand;
use hcf\command\faction\subcommands\UnClaimSubCommand;
use hcf\command\faction\subcommands\ClaimForSubCommand;
use hcf\command\faction\subcommands\DemoteSubCommand;
use hcf\command\faction\subcommands\DisbandAllSubCommand;
use hcf\command\faction\subcommands\ForceDisbandSubCommand;
use hcf\command\faction\subcommands\InfoSubCommand;
use hcf\command\faction\subcommands\KickSubCommand;
use hcf\command\faction\subcommands\LeaveSubCommand;
use hcf\command\faction\subcommands\ListSubCommand;
use hcf\command\faction\subcommands\PromoteSubCommand;
use hcf\command\faction\subcommands\RemoveDtrSubCommand;
use hcf\command\faction\subcommands\RemovePointsSubCommand;
use hcf\command\faction\subcommands\RemoveStrikesSubCommand;
use hcf\command\faction\subcommands\SetTimeSubCommand;
use hcf\command\faction\subcommands\UnfocusSubCommand;
use hcf\command\faction\subcommands\UpgradeSubCommand;
use hcf\command\faction\subcommands\WhoSubCommand;
use hcf\command\faction\subcommands\WithdrawSubCommand;
use hcf\command\pvp\subcommands\Enable;
use hcf\Loader;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class FactionCommand extends BaseCommand
{
    public function __construct(string $name, string $description = "")
    {
        parent::__construct(
            Loader::getInstance(),
            $name,
            $description
        );
    }

    protected function prepare(): void
    {
        $this->setPermission($this->getPermission());
        $this->registerSubCommand(new CreateSubCommand("create", "crea una faction"));
        $this->registerSubCommand(new DisbandSubCommand("disband", "elimina una faction"));
        $this->registerSubCommand(new ClaimSubCommand("claim", "para claimear una faction"));
        $this->registerSubCommand(new HelpSubCommand("help", "commandos de factions"));
        $this->registerSubCommand(new InviteSubCommand("invite", "para invitar una tu faction"));
        $this->registerSubCommand(new DepositSubCommand("deposit", "para depositar dinero a tu faction"));
        $this->registerSubCommand(new DepositSubCommand("d", "para depositar dinero a tu faction"));
        $this->registerSubCommand(new FocusSubCommand("focus", "coloca un focus ala faction"));
        $this->registerSubCommand(new HomeSubCommand("home", "te teletransporta hacia el home de tu faction"));
        $this->registerSubCommand(new JoinSubCommand("join", "para unirte a una faction"));
        $this->registerSubCommand(new SetHomeSubCommand("sethome", "coloca el home de tu faction"));
        $this->registerSubCommand(new StuckSubCommand("stuck", "te saca del territorio de un claim enemigo"));
        $this->registerSubCommand(new TopSubCommand("top", "te muestra las factions que estan en Top"));
        $this->registerSubCommand(new MapSubCommand("map", "muestra los limites de tu claim"));
        $this->registerSubCommand(new AddDtrSubCommand("adddtr", "Suma dtr a tu faction (solo administradores)"));
        $this->registerSubCommand(new ChatSubCommand("c", "para hablar solo con los de tu faction y para desactivar el faction chat"));
        $this->registerSubCommand(new UnClaimSubCommand("unclaim", "elimina el claim de tu faction"));
        $this->registerSubCommand(new CampSubCommand("camp", "te teletransporta cerca de la faction"));
        $this->registerSubCommand(new ClaimForSubCommand("claimfor", "para claimear roads spawn etc.(solo administradores)"));
        $this->registerSubCommand(new UnfocusSubCommand("unfocus", "quita el focus asia una faction"));
        $this->registerSubCommand(new WithdrawSubCommand("w", "para sacar dinero de tu faction"));
        $this->registerSubCommand(new InfoSubCommand("info", "muestra la informacion de tu faction"));
        $this->registerSubCommand(new WhoSubCommand("who", "muestra la informacion de las factions"));
        $this->registerSubCommand(new ListSubCommand("list", "muestra la lista de las factions"));
        $this->registerSubCommand(new AddStrikesSubCommand("addstrike", "Le agrega un strike a la faction"));
        $this->registerSubCommand(new RemoveStrikesSubCommand("remstrike", "remueve un strike ala faction"));
        $this->registerSubCommand(new AddPointsSubCommand("addpoints", "agrega points a la faction"));
        $this->registerSubCommand(new RemovePointsSubCommand("rempoints", "remueve points de la faction"));
        $this->registerSubCommand(new RemoveDtrSubCommand("remdtr", "quita dtr a una faction"));
        $this->registerSubCommand(new SetTimeSubCommand("settime", "setea el tiempo de regeneracion de una faction"));
        $this->registerSubCommand(new DisbandAllSubCommand("disbandall", "disbanea todas las factions"));
        $this->registerSubCommand(new ForceDisbandSubCommand("forcedisband", "borra la faction que elijas"));
        $this->registerSubCommand(new PromoteSubCommand("promote", "para promotear a un jugador de tu faction"));
        $this->registerSubCommand(new DemoteSubCommand("demote", "para demotear a un jugador de tu faction"));
        $this->registerSubCommand(new LeaveSubCommand("leave", "para salir de tu faction"));
        $this->registerSubCommand(new KickSubCommand("kick", "para sacar a un jugador de la faction"));
        $this->registerSubCommand(new UpgradeSubCommand("upgrade", "para mejorar tu claim con effectos"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        $sender->sendMessage(TextFormat::YELLOW."Use: /f help");
    }

    public function getPermission()
    {
        return "use.player.command";
    }
}