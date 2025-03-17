<?php

namespace Santy;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;

class Main extends PluginBase
{

    public function onEnable(): void
    {
        $this->getLogger()->info("ShopMsg plugin activado!");
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
    {
        if ($command->getName() === "shopalert") {
            if (!$sender->hasPermission("msgshop.perms")) {
                $sender->sendMessage(TextFormat::RED . "¡No tienes permiso para ejecutar este comando!");
                return false;
            }

            if (count($args) < 2) {
                $sender->sendMessage(TextFormat::YELLOW . "Uso incorrecto del comando. Usa: /shopalert <player> <cantidad> <article>");
                return false;
            }

            $playerN = $args[0];
            $cantidad = $args[1];
            $object = implode(" ", $args);
            if (empty($playerN)) {
                $sender->sendMessage(TextFormat::RED . "Proporsiona el nombre de el jugador");
            }

            if (!is_numeric($cantidad)){
                $sender->sendMessage(TextFormat::RED."Proporciona una cantidad valida (Solo numeros)");
            }

            foreach ($this->getServer()->getOnlinePlayers() as $player) {
                $player->sendMessage(TextFormat::GREEN . $playerN . " has Purchased x".$cantidad." ".$object." in nitromcpe.tebex.io  " . TextFormat::WHITE . $message);
            }

            $sender->sendMessage(TextFormat::GREEN . "El mensaje de tienda ha sido enviado a todos los jugadores.");
            return true;
        }

        return false;
    }
}
