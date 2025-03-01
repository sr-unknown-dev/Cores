<?php

declare(strict_types=1);

namespace hub\prefix\command;

use hub\entity\CustomItemEntity;
use hub\entity\TextEntity;
use hub\prefix\utils\Utils;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use hub\prefix\command\subcommand\CreateSubCommand;
use hub\prefix\command\subcommand\DeleteSubCommand;
use hub\prefix\command\subcommand\SetSubCommand;
use hub\prefix\command\subcommand\RemoveSubCommand;
use hub\prefix\command\subcommand\NpcSubCommand;
use hub\prefix\command\subcommand\ListSubCommand;

class PrefixCommand extends Command
{
    
    /** @var PrefixSubCommand[] */
    private array $subCommands = [];
    
    /**
     * PrefixCommand construct.
     */
    public function __construct()
    {
        parent::__construct('prefixes', 'Prefixes commands');
        $this->setAliases(['prefix']);
        $this->setPermission("prefix.command");
        
        $this->subCommands['create'] = new CreateSubCommand;
        $this->subCommands['delete'] = new DeleteSubCommand;
        $this->subCommands['set'] = new SetSubCommand;
        $this->subCommands['remove'] = new RemoveSubCommand;
        $this->subCommands['npc'] = new NpcSubCommand;
        $this->subCommands['list'] = new ListSubCommand;
    }
    
    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!isset($args[0])) {
            Utils::openPrefixMenu($sender);
            return;
        }
        $subCommand = $this->subCommands[$args[0]] ?? null;
        
        if ($subCommand === null) {
            $sender->sendMessage(TextFormat::colorize('&cThis sub command does not exist'));
            return;
        }
        array_shift($args);
        $subCommand->execute($sender, $args);
    }
}