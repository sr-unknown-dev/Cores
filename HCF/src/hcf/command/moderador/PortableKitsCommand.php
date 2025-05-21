<?php

namespace hcf\command\moderador;

use hcf\Factory;
use hcf\handler\kit\classes\presets\Archer;
use hcf\kits\ArcherOp;
use hcf\kits\BardOp;
use hcf\kits\Extreme;
use hcf\kits\Leviathan;
use hcf\kits\RogueOp;
use hcf\kits\Supreme;
use hcf\kits\hcf;
use hcf\kits\hcfPlus;
use hcf\player\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class PortableKitsCommand extends Command{

    public function __construct()
    {
        parent::__construct("kitsp", "Give to Kits portables", "/kitsp");
        $this->setPermission("moderador.command");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($args[0] === null) {
            return;
        }

        if ($sender instanceof Player) {
        switch ($args[0]) {
            case 'hcf+':
                Factory::hcfPlus($sender);
                break;
            case 'hcf':
                Factory::hcf($sender);
                break;
            case 'leviathan':
                Factory::Leviathan($sender);
                break;
            case 'supreme':
                Factory::Supreme($sender);
                break;
            case 'extreme':
                Factory::Extreme($sender);
                break;
            case 'archerop':
                Factory::ArcherOp($sender);
                break;
            case 'bardop':
                Factory::BardOp($sender);
                break;
            case 'rogueop':
                Factory::RogueOp($sender);
                break;
            case 'all':
                Factory::AllKits($sender);
                break;
            
            
            default:
                # code...
                break;
            }
        }
    }
}