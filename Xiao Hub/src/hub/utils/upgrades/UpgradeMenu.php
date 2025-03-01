<?php 
    
namespace hub\utils\upgrades;

use hub\Loader;
use hub\player\Player;

use pocketmine\utils\TextFormat;
use pocketmine\item\VanillaItems;
use pocketmine\block\VanillaBlocks;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use muqsit\invmenu\type\InvMenuTypeIds;

class UpgradeMenu {
    
    static function create(Player $player): void {
        $glass = VanillaBlocks::STAINED_GLASS_PANE()->asItem();
        $glass->setCustomName(TextFormat::colorize(" "));
        $speed = VanillaItems::SUGAR();
        $speed->setCustomName(TextFormat::colorize("&r&3Speed II\n&bPrice: &a70000"));
        $resis = VanillaItems::IRON_INGOT();
        $resis->setCustomName(TextFormat::colorize("&r&7Resistance I\n&bPrice: &a70000"));
        $fuerza = VanillaItems::GUNPOWDER();
        $fuerza->setCustomName(TextFormat::colorize("&r&gStrength I\n&bPrice: &a70000"));
        $salto = VanillaItems::FEATHER();
        $salto->setCustomName(TextFormat::colorize("&r&4Jump Boost II\n&bPrice: &a70000"));
        $menu = InvMenu::create(InvMenuTypeIds::TYPE_CHEST);
        $menu->setName("§gFaction Upgrade");
        $menu->getInventory()->setContents([
            0 => $glass,
            1 => $glass,
            2 => $glass,
            3 => $glass,
            4 => $glass,
            5 => $glass,
            6 => $glass,
            7 => $glass,
            8 => $glass,
            9 => $glass,
            17 => $glass,
            18 => $glass,
            19 => $glass,
            20 => $glass,
            21 => $glass,
            22 => $glass,
            23 => $glass,
            24 => $glass,
            25 => $glass,
            26 => $glass,
            10 => $speed,
            12 => $resis,
            14 => $fuerza,
            16 => $salto
        ]);
        $menu->setListener(function (InvMenuTransaction $transaction): InvMenuTransactionResult {
            $player = $transaction->getPlayer();
            if ($transaction->getItemClicked()->getCustomName() === "§r§3Speed II\n§bPrice: §a70000") {
                $session = Loader::getInstance()->getSessionManager()->getSession($player->getXuid());
                $balance = $session->getBalance();
                $data = Loader::getInstance()->getFactionManager()->getFaction($session->getFaction());
                if ($data->isSpeedUpgrade() === true) {
                $player->sendMessage(TextFormat::colorize("&cThis can only be used once."));
                return $transaction->discard();
            }
                if($balance < 70000){
                    $player->sendMessage(TextFormat::colorize("&cYou don't have enough money."));
                    return $transaction->discard();
                }
                if ($balance >= 70000) {
                    $session->setBalance($session->getBalance() - 70000);
                    $data->setUpgradeSpeed(true);
                    $player->sendMessage(TextFormat::colorize("&aUpgrade placed on your Faction correctly"));
                    $player->removeCurrentWindow();
                }
            }
            if ($transaction->getItemClicked()->getCustomName() === "§r§7Resistance I\n§bPrice: §a70000") {
                $session = Loader::getInstance()->getSessionManager()->getSession($player->getXuid());
                $balance = $session->getBalance();
                $data = Loader::getInstance()->getFactionManager()->getFaction($session->getFaction());
                if ($data->isResistanceUpgrade() === true) {
                $player->sendMessage(TextFormat::colorize("&cThis can only be used once."));
                return $transaction->discard();
            }
                if($balance < 70000){
                    $player->sendMessage(TextFormat::colorize("&cYou don't have enough money."));
                    return $transaction->discard();
                }
                if ($balance >= 70000) {
                    $session->setBalance($session->getBalance() - 70000);
                    $data->setUpgradeResistance(true);
                    $player->sendMessage(TextFormat::colorize("&aUpgrade placed on your Faction correctly"));
                    $player->removeCurrentWindow();
                }
            }
            if ($transaction->getItemClicked()->getCustomName() === "§r§gStrength I\n§bPrice: §a70000") {
                $session = Loader::getInstance()->getSessionManager()->getSession($player->getXuid());
                $balance = $session->getBalance();
                $data = Loader::getInstance()->getFactionManager()->getFaction($session->getFaction());
                if ($data->isStrengthUpgrade() === true) {
                $player->sendMessage(TextFormat::colorize("&cThis can only be used once."));
                return $transaction->discard();
            }
                if($balance < 70000){
                    $player->sendMessage(TextFormat::colorize("&cYou don't have enough money."));
                    return $transaction->discard();
                }
                if ($balance >= 70000) {
                    $session->setBalance($session->getBalance() - 70000);
                    $data->setUpgradeStrength(true);
                    $player->sendMessage(TextFormat::colorize("&aUpgrade placed on your Faction correctly"));
                    $player->removeCurrentWindow();
                }
            }
            if ($transaction->getItemClicked()->getCustomName() === "§r§4Jump Boost II\n§bPrice: §a70000") {
                $session = Loader::getInstance()->getSessionManager()->getSession($player->getXuid());
                $balance = $session->getBalance();
                $data = Loader::getInstance()->getFactionManager()->getFaction($session->getFaction());
                if ($data->isJumpUpgrade() === true) {
                $player->sendMessage(TextFormat::colorize("&cThis can only be used once."));
                return $transaction->discard();
            }
                if($balance < 70000){
                    $player->sendMessage(TextFormat::colorize("&cYou don't have enough money."));
                    return $transaction->discard();
                }
                if ($balance >= 70000) {
                    $session->setBalance($session->getBalance() - 70000);
                    $data->setUpgradeJump(true);
                    $player->sendMessage(TextFormat::colorize("&aUpgrade placed on your Faction correctly"));
                    $player->removeCurrentWindow();
                }
            }
            return $transaction->discard();
        });
        $menu->send($player);
    }
    
}