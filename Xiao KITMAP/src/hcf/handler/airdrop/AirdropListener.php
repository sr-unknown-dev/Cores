<?php

namespace hcf\handler\airdrop;

use hcf\Loader;
use pocketmine\block\tile\Chest;
use pocketmine\block\VanillaBlocks;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\TextFormat;
use pocketmine\world\particle\ExplodeParticle;
use pocketmine\world\particle\FloatingTextParticle;
use pocketmine\world\sound\ExplodeSound;

class AirdropListener implements Listener
{
    public function handlePlace(BlockPlaceEvent $event): void{
        $item = $event->getItem();
        $player = $event->getPlayer();
        $targetBlock = $player->getTargetBlock(3);
        $location = $targetBlock->getPosition();

        if (AirdropManager::getAirdrop()->getItems() === null){
            $player->sendMessage(TextFormat::RED."No se puedo colocar el airdrop, avisa a un staff o owner para que solucione el problema");
            return;
        }

        if ($item->getNamedTag()->getTag("Airdrop_Item") && $item->getNamedTag()->getString("Airdrop_Item") === "Airdrop"){
            $chestPos = $location->add(0, 1, 0);
            $player->getWorld()->setBlock($chestPos, VanillaBlocks::CHEST());
            $location->getWorld()->addSound($chestPos, new ExplodeSound());
            $location->getWorld()->addParticle($chestPos, new ExplodeParticle());
            $tile = $player->getWorld()->getTile($chestPos);

            if ($tile instanceof Chest) {
                $inventory = $tile->getInventory();
                for ($i=0; $i < 27; $i++){
                    $items = AirdropManager::getAirdrop()->getRandomItems();

                    if ($items !== null){
                        $inventory->setItem($i, $items);
                        $tile->setName("§l§3Airdrop");
                    }

                    $item = $event->getItem();
                    $item->pop();
                    $player->getInventory()->setItemInHand($item);
                }

                $world = $player->getWorld();
                $TextPosition = $chestPos->add(0.5, 1.5, 0.5);
                $floatingText = new FloatingTextParticle("§l§3Airdrop", "");
                $world->addParticle($TextPosition, $floatingText);

                Loader::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function() use ($world, $TextPosition, $floatingText): void {
                    $floatingText->setText("");
                    $floatingText->setTitle("");
                    $world->addParticle($TextPosition, $floatingText);
                }), 120);
                $event->cancel();
            }else{
                $player->sendMessage(TextFormat::RED."No se pudo colocar el airdrop");
            }
            return;
        }
    }
}