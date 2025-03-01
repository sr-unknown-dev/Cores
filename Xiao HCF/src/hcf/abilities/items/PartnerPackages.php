<?php

namespace hcf\abilities\items;

use hcf\entity\projectiles\FireworksRocket;
use hcf\handler\package\PackageManager;
use hcf\Loader;
use hcf\item\Fireworks;
use pocketmine\block\VanillaBlocks;
use pocketmine\entity\Location;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemTypeIds;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\protocol\types\BlockPosition;
use pocketmine\network\mcpe\protocol\UpdateBlockPacket;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\world\Position;

use function pocketmine\server;

class PartnerPackages implements Listener {

    public static function addPartner(Player $player, int $int): void {
        $partner = VanillaBlocks::ENDER_CHEST()->asItem()->setCount($int);
        $partner->setCustomName(TextFormat::BOLD . TextFormat::LIGHT_PURPLE . "Partner Packages");
        $partner->setLore(["\n§r§7Right click to receive different types of Ability Items.\n§ePurchase at §3https://" . Loader::getInstance()->getConfig()->get('tebex')]);
        $partner->addEnchantment(new EnchantmentInstance(VanillaEnchantments::FORTUNE(), 1));
        $namedtag = $partner->getNamedTag();
        $namedtag->setString('pp_packages', 'pp');
        $partner->setNamedTag($namedtag);
        $player->getInventory()->addItem($partner);
    }

    public function Place(BlockPlaceEvent $event): void {
        $player = $event->getPlayer();
        $item = $event->getItem();

        if ($item->getNamedTag()->getTag('pp_packages') !== null) {
            $event->cancel();
            $pos = $event->getBlockAgainst()->getPosition();
            $pk = new UpdateBlockPacket();
            $pk->blockPosition = BlockPosition::fromVector3($pos->asVector3());
            $pk->blockRuntimeId = VanillaBlocks::AIR()->getTypeId();
            $player->getNetworkSession()->sendDataPacket($pk);
            $this->spawnFirework($pos, $player);

            if (count(PackageManager::getPartnerPackage()->getItems()) == 0)
                return;

            if (!$player->getInventory()->canAddItem(PackageManager::getPartnerPackage()->getRandomItems())) {
                $player->sendMessage(TextFormat::RED . "Your inventory is full!");
                return;
            }

            $item1 = PackageManager::getPartnerPackage()->getRandomItems();
            $player->getInventory()->addItem($item1);

            if($item->getCount() > 1){
                $item->setCount($item->getCount() - 1);
            } else {
                $item = VanillaItems::AIR();
            }
            
            $player->getInventory()->setItemInHand($item);
        }
    }

    public function spawnFirework(Position $pos) {
        
        $data = new Fireworks(new ItemIdentifier(ItemTypeIds::newId()), 'firework');
        $data->addExplosion(4, "", "");
        $entity = new FireworksRocket(Location::fromObject($pos->add(0.5, 0, 0.5), $pos->getWorld(), lcg_value() * 360, 90), $data);
        $entity->spawnToAll();
	}

}