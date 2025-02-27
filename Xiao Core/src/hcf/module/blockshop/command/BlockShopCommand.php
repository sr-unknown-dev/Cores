<?php

declare(strict_types=1);

namespace hcf\module\blockshop\command;

use hcf\entity\server\AbilityEntity;
use hcf\entity\server\BountyEntity;
use hcf\entity\server\DailyEntity;
use hcf\entity\server\FixallEntity;
use hcf\entity\server\InfoEntity;
use hcf\entity\server\PortableKitsEntity;
use hcf\entity\server\SupportEntity;
use hcf\entity\tops\TopFactionsEntity;
use hcf\entity\tops\TopKDREntity;
use hcf\entity\tops\TopKillsEntity;
use hcf\module\blockshop\entity\ShopAndSellEntity;
use hcf\module\blockshop\utils\Utils as Utilshop;
use hcf\player\Player;
use hcf\utils\Utils;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\utils\MobHeadType;
use pocketmine\block\VanillaBlocks;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\VanillaItems;

class BlockShopCommand extends Command
{

    /**
     * BlockShopCommand construct.
     */
    public function __construct()
    {
        parent::__construct('npc', 'Command for blockshop');
        $this->setPermission('npc.command');
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void {

        if(!$sender instanceof Player) return;

        $menu = InvMenu::create(InvMenu::TYPE_CHEST);
        $menu->setName("§7Npc");
        $menu->getInventory()->setContents([
            11 => VanillaItems::WRITTEN_BOOK()->setCustomName("§cCreate NPC")->setLore(["§r§7settings to spawn the type of§cnpc", "§r§7currently available", "§r§7configure npc"]),
            13 => VanillaBlocks::MOB_HEAD()->setMobHeadType(mobHeadType::PLAYER())->asItem()->setCustomName("§cexclusive for owner")->setLore(["§r§7Configuration in beta, more functions will soon be added to", "§r§7customize and improve the appearance of the server"]),
            15 => VanillaItems::GOLDEN_HOE()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::INFINITY(), 1))->setCustomName("§eRemove NPC")->setLore(["§r§7remove currently available npc type", "§r§7configure npc"]),
        ]);

        $menu->setListener(function(InvMenuTransaction $transaction) use ($menu) : InvMenuTransactionResult{
            $player = $transaction->getPlayer();

            if($transaction->getItemClicked()->getCustomName() === "§cCreate NPC"){
                $menu->getInventory()->clearAll();
                $menu->getInventory()->setContents([
                    12 => VanillaItems::PAPER()->setCustomName("§gServer NPC")->setLore(["§r§7settings to spawn the type of§cnpc", "§r§7currently available", "§r§7configure npc"]),
                    14 => VanillaItems::PAPER()->setCustomName("§gTops NPC")->setLore(["§r§7settings to spawn the type of§cnpc", "§r§7currently available", "§r§7configure npc"]),
                    18 => VanillaItems::WRITABLE_BOOK()->setCustomName("§cGo Back")->setLore(["§r§7Return page"]),
                ]);
            }

            if($transaction->getItemClicked()->getCustomName() === "§gServer NPC"){
                $menu->getInventory()->clearAll();
                $menu->getInventory()->setContents([
                    10 => VanillaItems::PAPER()->setCustomName("§gShop NPC")->setLore(["§r§7settings to spawn the type of§cnpc", "§r§7currently available", "§r§7configure npc"]),
                    12 => VanillaItems::PAPER()->setCustomName("§gFixall NPCS")->setLore(["§r§7settings to spawn the type of§cnpc", "§r§7currently available", "§r§7configure npc"]),
                    13 => VanillaItems::PAPER()->setCustomName("§gInfo NPC")->setLore(["§r§7settings to spawn the type of§cnpc", "§r§7currently available", "§r§7configure npc"]),
                    14 => VanillaItems::PAPER()->setCustomName("§gAbilitys NPC")->setLore(["§r§7settings to spawn the type of§cnpc", "§r§7currently available", "§r§7configure npc"]),
                    15 => VanillaItems::PAPER()->setCustomName("§gSupport NPC")->setLore(["§r§7settings to spawn the type of§cnpc", "§r§7currently available", "§r§7configure npc"]),
                    16 => VanillaItems::PAPER()->setCustomName("§gBounty NPC")->setLore(["§r§7settings to spawn the type of§cnpc", "§r§7currently available", "§r§7configure npc"]),
                    18 => VanillaItems::WRITABLE_BOOK()->setCustomName("§r§cGo Back")->setLore(["§r§7Return page"]),
                    
                ]);
            }

            if($transaction->getItemClicked()->getCustomName() === "§gTops NPC"){
                $menu->getInventory()->clearAll();
                $menu->getInventory()->setContents([
                    11 => VanillaItems::PAPER()->setCustomName("§gTop Kills NPC")->setLore(["§r§7settings to spawn the type of§cnpc", "§r§7currently available", "§r§7configure npc"]),
                    13 => VanillaItems::PAPER()->setCustomName("§gTop factions NPC")->setLore(["§r§7settings to spawn the type of§cnpc", "§r§7currently available", "§r§7configure npc"]),
                    15 => VanillaItems::PAPER()->setCustomName("§gTop KDR NPC")->setLore(["§r§7settings to spawn the type of§cnpc", "§r§7currently available", "§r§7configure npc"]),
                    18 => VanillaItems::WRITABLE_BOOK()->setCustomName("§r§cGo Back")->setLore(["§r§7Return page"]),
                ]);
            }
            if($transaction->getItemClicked()->getCustomName() === "§gShop NPC"){
                $entity = new ShopAndSellEntity($player->getLocation(), $player->getSkin(), Utilshop::createBasicNBT($player));
                $entity->spawnToAll();
                $transaction->getPlayer()->removeCurrentWindow();
                Utils::PlaySound($player, "random.orb", 1, 1);
            }

            if($transaction->getItemClicked()->getCustomName() === "§gInfo NPC"){
                $entity = InfoEntity::create($player);
                $entity->spawnToAll();
                $transaction->getPlayer()->removeCurrentWindow();
                Utils::PlaySound($player, "random.orb", 1, 1);
            }

            if($transaction->getItemClicked()->getCustomName() === "§gAbilitys NPC"){
                $entity = AbilityEntity::create($player);
                $entity->spawnToAll();
                $transaction->getPlayer()->removeCurrentWindow();
                Utils::PlaySound($player, "random.orb", 1, 1);
            }

            if($transaction->getItemClicked()->getCustomName() === "§gBounty NPC"){
                $entity = BountyEntity::create($player);
                $entity->spawnToAll();
                $transaction->getPlayer()->removeCurrentWindow();
                Utils::PlaySound($player, "random.orb", 1, 1);
            }

            if($transaction->getItemClicked()->getCustomName() === "§gTop Kills NPC"){
                $entity = TopKillsEntity::create($player);
                $entity->spawnToAll();
                $transaction->getPlayer()->removeCurrentWindow();
                Utils::PlaySound($player, "random.orb", 1, 1);
            }

            if($transaction->getItemClicked()->getCustomName() === "§gSupport NPC"){
                $entity = SupportEntity::create($player);
                $entity->spawnToAll();
                $transaction->getPlayer()->removeCurrentWindow();
                Utils::PlaySound($player, "random.orb", 1, 1);
            }

            if($transaction->getItemClicked()->getCustomName() === "§gFixall NPCS"){
                $entity = FixallEntity::create($player);
                $entity->spawnToAll();
                $transaction->getPlayer()->removeCurrentWindow();
                Utils::PlaySound($player, "random.orb", 1, 1);
            }

            if($transaction->getItemClicked()->getCustomName() === "§gTop factions NPC"){
                $entity = TopFactionsEntity::create($player);
                $entity->spawnToAll();
                $transaction->getPlayer()->removeCurrentWindow();
                Utils::PlaySound($player, "random.orb", 1, 1);
            }

            if($transaction->getItemClicked()->getCustomName() === "§gTop KDR NPC"){
                $entity = TopKDREntity::create($player);
                $entity->spawnToAll();
                $transaction->getPlayer()->removeCurrentWindow();
                Utils::PlaySound($player, "random.orb", 1, 1);
            }

            if($transaction->getItemClicked()->getCustomName() === "§eRemove NPC"){
                $player->getInventory()->addItem(VanillaItems::GOLDEN_HOE()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::FEATHER_FALLING(), 1))->setCustomName("§eRemove NPC §r§7(Right Click)")->setLore(["§r§7remove currently available npc type", "§r§7configure npc"]));
                $transaction->getPlayer()->removeCurrentWindow();
                Utils::PlaySound($player, "random.orb", 1, 1);
            }

            if($transaction->getItemClicked()->getCustomName() === "§cGo Back"){
                $menu->getInventory()->clearAll();
                $menu->getInventory()->setContents([
                    11 => VanillaItems::WRITTEN_BOOK()->setCustomName("§cCreate NPC")->setLore(["§r§7settings to spawn the type of§cnpc", "§r§7currently available", "§r§7configure npc"]),
                    13 => VanillaBlocks::MOB_HEAD()->setMobHeadType(mobHeadType::PLAYER())->asItem()->setCustomName("§cexclusive for owner")->setLore(["§r§7Configuration in beta, more functions will soon be added to", "§r§7customize and improve the appearance of the server", "§r§7Youtube: §5Zenji §7(zDarxsEz)"]),
                    15 => VanillaItems::GOLDEN_HOE()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::INFINITY(), 1))->setCustomName("§eRemove NPC")->setLore(["§r§7remove currently available npc type", "§r§7configure npc"]),
                ]);
                Utils::PlaySound($player, "random.pop", 1, 1);
            }

            if($transaction->getItemClicked()->getCustomName() === "§r§cGo Back"){
                $menu->getInventory()->clearAll();
                $menu->getInventory()->setContents([
                    12 => VanillaItems::PAPER()->setCustomName("§gServer NPC")->setLore(["§r§7settings to spawn the type of§cnpc", "§r§7currently available", "§r§7configure npc"]),
                    14 => VanillaItems::PAPER()->setCustomName("§gTops NPC")->setLore(["§r§7settings to spawn the type of§cnpc", "§r§7currently available", "§r§7configure npc"]),
                    18 => VanillaItems::WRITABLE_BOOK()->setCustomName("§cGo Back")->setLore(["§r§7Return page"]),
                ]);
                Utils::PlaySound($player, "random.pop", 1, 1);
            }
            return $transaction->discard();
        });
        $menu->send($sender);
    }
}
