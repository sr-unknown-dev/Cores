<?php

declare(strict_types=1);

namespace hcf\command;

use hcf\command\moderador\AlertCommand;
use hcf\command\moderador\CooldownCommand as ModeradorCooldownCommand;
use hcf\command\moderador\EnchantAllCommand;
use hcf\command\moderador\GiveMoneyCommand;
use hcf\command\moderador\GodCommand;
use hcf\command\moderador\kitsnpc\KitNPCCommand;
use hcf\command\moderador\NickCommand;
use hcf\command\moderador\PortableKitsCommand;
use hcf\command\moderador\TpallCommand;
use hcf\Loader;

/**
 * Class CommandManager
 * @package hcf\command
 */
class CommandManager
{
    
    /**
     * CommandManager construct.
     */
    public function __construct()
    {
        Loader::getInstance()->getServer()->getCommandMap()->register("HCF", new FreeRankCommand("freerank"));
        Loader::getInstance()->getServer()->getCommandMap()->register("HCF", new SetCoinsCommand());
        Loader::getInstance()->getServer()->getCommandMap()->register("HCF", new SetBalanceCommand());
        Loader::getInstance()->getServer()->getCommandMap()->register("HCF", new CoinsCommand());
        Loader::getInstance()->getServer()->getCommandMap()->register('HCF', new ListCommand());
        Loader::getInstance()->getServer()->getCommandMap()->register('HCF', new ECCommand());
        Loader::getInstance()->getServer()->getCommandMap()->register('HCF', new BalanceCommand());
        Loader::getInstance()->getServer()->getCommandMap()->register('HCF', new LogoutCommand());
        Loader::getInstance()->getServer()->getCommandMap()->register('HCF', new NearCommand());
        Loader::getInstance()->getServer()->getCommandMap()->register('HCF', new RenameCommand());
        Loader::getInstance()->getServer()->getCommandMap()->register('HCF', new AutoFeedCommand());
        Loader::getInstance()->getServer()->getCommandMap()->register('HCF', new TLCommand());
        Loader::getInstance()->getServer()->getCommandMap()->register('HCF', new FeedCommand());
        Loader::getInstance()->getServer()->getCommandMap()->register('HCF', new LeaderboardsCommands());
        Loader::getInstance()->getServer()->getCommandMap()->register('HCF', new ClearEntitiesCommand());
        Loader::getInstance()->getServer()->getCommandMap()->register('HCF', new ScoreboardCommand());
        Loader::getInstance()->getServer()->getCommandMap()->register('HCF', new BrewerCommand());
        Loader::getInstance()->getServer()->getCommandMap()->register('HCF', new SpawnCommand());
        Loader::getInstance()->getServer()->getCommandMap()->register('HCF', new RallyCommand());
        Loader::getInstance()->getServer()->getCommandMap()->register('HCF', new LivesCommand());
        Loader::getInstance()->getServer()->getCommandMap()->register('HCF', new LFFCommand());
        Loader::getInstance()->getServer()->getCommandMap()->register('HCF', new ShopCommand());


        // moderador commands
        Loader::getInstance()->getServer()->getCommandMap()->registerAll('HCF', [
            new GodCommand(),
            new AlertCommand(),
            new TpallCommand(),
            new GiveMoneyCommand(),
            new PingCommand(),
            new PortableKitsCommand(),
            new NickCommand(),
            new ModeradorCooldownCommand(),
            new EnchantAllCommand(),
            new KitNPCCommand(),
        ]);
    }
}