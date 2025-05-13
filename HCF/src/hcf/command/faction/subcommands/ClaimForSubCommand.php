<?php

namespace hcf\command\faction\subcommands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use hcf\Loader;
use hcf\player\Player;
use hcf\Server\ServerA;
use pocketmine\command\CommandSender;
use pocketmine\item\VanillaItems;
use pocketmine\utils\TextFormat;

class ClaimForSubCommand extends BaseSubCommand
{

	private array $claims = [
        'Spawn' => 'spawn',
        'North Road' => 'road',
        'South Road' => 'road',
        'West Road' => 'road',
        'East Road' => 'road',
        'Citadel' => 'citadel',
    ];

    public function __construct(string $name, string $description = "", array $aliases = [])
    {
        parent::__construct($name, $description, $aliases);
    }

    protected function prepare(): void
    {
        $this->setPermission($this->getPermission());
        $this->registerArgument(0, new RawStringArgument("claimName", true));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender instanceof Player)
            return;
            
        if (!$sender->hasPermission('faction.command.claimfor')) {
            $sender->sendMessage(TextFormat::colorize('&cThis sub command does not exist'));
            return;
        }
        
        if (count($args) < 1) {
            if (($creator = Loader::getInstance()->getClaimManager()->getCreator($sender->getName())) !== null) {
                if (!$creator->isValid()) {
                    $sender->sendMessage(TextFormat::colorize('&cYou have not selected the claim'));
                    return;
                }
                $creator->deleteCorners($sender);
                Loader::getInstance()->getClaimManager()->createClaim($creator->getName(), $creator->getType(), $creator->getMinX(), $creator->getMaxX(), $creator->getMinZ(), $creator->getMaxZ(), $creator->getWorld());
                $sender->sendMessage(TextFormat::colorize('&aYou have made the claim of the opclaim ' . $creator->getName()));
                Loader::getInstance()->getClaimManager()->removeCreator($sender->getName());
            
                foreach ($sender->getInventory()->getContents() as $slot => $i) {
                    if ($i->getNamedTag()->getTag('claim_type')) {
                        $sender->getInventory()->clear($slot);
                        break;
                    }
                }
                return;
            }

            $sender->sendMessage(TextFormat::colorize('&cUse /faction claimfor [string: name]'));
            return;
        }
        $claimName = $args["claimName"];
        
        if ($claimName === 'cancel') {
            if (($creator = Loader::getInstance()->getClaimManager()->getCreator($sender->getName())) !== null && $creator->getType() === $this->claims[$creator->getName()]) {
                $creator->deleteCorners($sender);
                Loader::getInstance()->getClaimManager()->removeCreator($sender->getName());
                $sender->sendMessage(TextFormat::colorize('&cYou have canceled the claim'));
            } else
                $sender->sendMessage(TextFormat::colorize('&cYou are not in claim mode yet'));
            return;
        }
        
        if (Loader::getInstance()->getClaimManager()->getCreator($sender->getName()) !== null) {
            $sender->sendMessage(TextFormat::colorize('&cYou are already creating a claim'));
            return;
        }
        
        if (!isset($this->claims[$claimName])) {
            $claimType = 'custom';
        }else{
            $claimType = $this->claims[$claimName];
        }
        
        if (Loader::getInstance()->getFactionManager()->getFaction($claimName) === null)
            Loader::getInstance()->getFactionManager()->createFaction($claimName, [
               'roles' => [],
               'dtr' => 1.01,
               'balance' => 0,
               'points' => 0,
               'kothCaptures' => 0,
               'timeRegeneration' => null,
               'home' => null,
               'claim' => null
           ]);
        $item = VanillaItems::GOLDEN_HOE()->setCustomName(TextFormat::colorize('&eClaim selector'));
        $item->setNamedTag($item->getNamedTag()->setString('claim_type', $claimType));
        ServerA::$claim[$sender->getName()] = true;
        
        if (!$sender->getInventory()->canAddItem($item)) {
            $sender->sendMessage(TextFormat::colorize('&cYou cannot add the item to make the claim to your inventory'));
            return;
        }
        $sender->getInventory()->addItem($item);
        Loader::getInstance()->getClaimManager()->createCreator($sender->getName(), $claimName, $claimType);
        $sender->sendMessage(TextFormat::colorize('&aNow you can claim the area'));
    }

    public function getPermission(): ?string
    {
        return "use.player.command";
    }
}