<?php

declare(strict_types=1);

namespace hcf\timer\command;

use hcf\Loader;
use hcf\utils\time\Timer;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

/**
 * Class DeathCommand
 * @package hcf\timer\command
 */
class DeathCommand extends Command
{
    
    /**
     * DeathCommand construct.
     */
    public function __construct()
    {
        parent::__construct('fury', '§bCommand for event fury');
        $this->setPermission('fury.command');
    }
    
    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$this->testPermission($sender))
            return;
            
        if (count($args) < 1) {
            $sender->sendMessage(TextFormat::colorize('&cUse /fury help'));
            return;
        }
        
        switch (strtolower($args[0])) {
            case 'help':
                $sender->sendMessage(
                    TextFormat::colorize('&eFury Commands') . "\n" .
                    TextFormat::colorize('&7/fury start [time] - &eUse this command to start the Fury Event') . "\n" .
                    TextFormat::colorize('&7/fury stop - &eUse this command to stop fury')
                );
                break;
            
            case 'start':
                if (Loader::getInstance()->getTimerManager()->getDeath()->isActive()) {
                    $sender->sendMessage(TextFormat::colorize('&cThe fury is already started'));
                    return;
                }
                
                if (count($args) < 2) {
                    $sender->sendMessage(TextFormat::colorize('&cUse /fury start [time]'));
                    return;
                }
                $time = $args[1];
                
                $time = Timer::time($time);
                Loader::getInstance()->getTimerManager()->getDeath()->setActive(true);
                Loader::getInstance()->getTimerManager()->getDeath()->setTime((int) $time);
                $sender->sendMessage(TextFormat::colorize('&aThe fury has started!'));
                break;
            
            case 'stop':
                if (!Loader::getInstance()->getTimerManager()->getDepuracion()->isActive()) {
                    $sender->sendMessage(TextFormat::colorize('&cThe fury has not started'));
                    return;
                }
                Loader::getInstance()->getTimerManager()->getDeath()->setActive(false);
                $sender->sendMessage(TextFormat::colorize('&cYou have turned off the death'));
                break;
            
            default:
                $sender->sendMessage(TextFormat::colorize('&cUse /death help'));
                break;
        }
    }
}