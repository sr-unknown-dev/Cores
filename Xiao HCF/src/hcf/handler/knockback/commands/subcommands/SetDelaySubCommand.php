<?php

namespace hcf\handler\knockback\commands\subcommands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\Config;

class SetDelaySubCommand extends BaseSubCommand {

    public function __construct(string $name, string $description = "", array $aliases = []) {
        parent::__construct($name, $description, $aliases);
    }

    public function prepare(): void {
        $this->setPermission("knockback.command.delay");
        $this->registerArgument(0, new RawStringArgument("valor", true));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
        if (!$sender instanceof Player) {
            $sender->sendMessage("Este comando solo puede ser ejecutado por un jugador.");
            return;
        }

        if (!isset($args["valor"])) {
            $sender->sendMessage("Uso: /knockback delay <valor>");
            return;
        }
        
        $valor = (float)$args["valor"];
        
        $config = new Config("knockback.yml", Config::YAML);
        
        // Actualizar el valor del retraso de knockback
        $config->setNested("knockback.delay", $valor);
        
        // Guardar la configuración actualizada
        $config->save();
        
        $sender->sendMessage("Retraso de knockback establecido a: " . $valor);
    }
}
