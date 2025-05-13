<?php

namespace hcf\command\msg;

use CortexPE\Commando\BaseCommand;
use hcf\arguments\MsgArgument;
use hcf\Loader;
use hcf\player\Player;
use hcf\Server\ServerA;
use pocketmine\command\CommandSender;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class ReplyCommand extends BaseCommand
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
        $this->registerArgument(0, new MsgArgument("message"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        $msg = $args["message"];

        if (isset(ServerA::$chatr[$sender->getName()])) {
            $receiverName = ServerA::$chatr[$sender->getName()]['sender'];
            $receiver = Server::getInstance()->getPlayerExact($receiverName);
            
            if ($receiver instanceof Player && $receiver->isOnline()) {
                $sender->sendMessage(TextFormat::colorize("&8(&gTo&8) &g".$receiver->getName().": &7".$msg));
                $receiver->sendMessage(TextFormat::colorize("&8(&gFrom&8) &g".$sender->getName().": &7".$msg));

                ServerA::$chatr[$receiver->getName()] = [
                    'sender' => $sender->getName(),
                    'receiver' => $receiver->getName()
                ];
            } else {
                $sender->sendMessage(TextFormat::RED . "El jugador ya no está en línea.");
            }
        } else {
            $sender->sendMessage(TextFormat::RED . "No tienes a nadie a quien responder.");
        }
    }

    public function getPermission()
    {
        return "use.player.command";
    }
}