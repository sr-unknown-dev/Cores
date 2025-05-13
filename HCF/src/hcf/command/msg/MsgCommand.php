<?php

namespace hcf\command\msg;

use CortexPE\Commando\BaseCommand;
use hcf\arguments\MsgArgument;
use hcf\arguments\PlayersArgument;
use hcf\Factory;
use hcf\Loader;
use hcf\player\Player;
use hcf\Server\Chatr;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class MsgCommand extends BaseCommand
{
    public function __construct(string $name, string $description = "")
    {
        parent::__construct(Loader::getInstance(), $name, $description);
    }

    protected function prepare(): void
    {
        $this->setPermission("use.player.command");
        $this->registerArgument(0, new PlayersArgument("player", true));
        $this->registerArgument(1, new MsgArgument("message", true));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!isset($args["player"]) || !isset($args["message"])) {
            $sender->sendMessage(TextFormat::RED . "Uso: /msg <jugador> <mensaje>");
            return;
        }

        $player = $args["player"];
        $msg = $args["message"];
        
        if (!$player instanceof Player) {
            $sender->sendMessage(TextFormat::RED . "Jugador no encontrado.");
            return;
        }

        if ($player === $sender) {
            $sender->sendMessage(TextFormat::RED . "No puedes enviarte mensajes a ti mismo.");
            return;
        }

        $sender->sendMessage(TextFormat::colorize("&8(&gTo&8) &g".$player->getName().": &7".$msg));
        $player->sendMessage(TextFormat::colorize("&8(&gFrom&8) &g".$sender->getName().": &7".$msg));

        Chatr::$chatr[$sender->getName()] = [
            'sender' => $player->getName(),
            'receiver' => $sender->getName()
        ];
    }

    public function getPermission(): string
    {
        return "use.player.command";
    }
}
