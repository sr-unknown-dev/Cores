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
    public function handlePlace(BlockPlaceEvent $event): void
    {
        $item = $event->getItem();

        // Verificar primero si es un Airdrop para evitar procesamiento innecesario
        if (!$item->getNamedTag()->getTag("Airdrop_Item") ||
            $item->getNamedTag()->getString("Airdrop_Item") !== "Airdrop") {
            return;
        }

        $player = $event->getPlayer();
        $targetBlock = $player->getTargetBlock(3);
        $location = $targetBlock->getPosition();

        // Validar items al inicio
        $airdropItems = AirdropManager::getAirdrop()->getItems();
        if ($airdropItems === null) {
            $player->sendMessage(TextFormat::RED . "No se pudo colocar el airdrop, avisa a un staff o owner para que solucione el problema");
            return;
        }

        $chestPos = $location->add(0, 1, 0);
        $world = $player->getWorld();

        // Colocar el bloque
        $world->setBlock($chestPos, VanillaBlocks::CHEST());

        // Agrupar efectos
        $world->addSound($chestPos, new ExplodeSound());
        $world->addParticle($chestPos, new ExplodeParticle());

        $tile = $world->getTile($chestPos);

        if (!($tile instanceof Chest)) {
            $player->sendMessage(TextFormat::RED . "No se pudo colocar el airdrop");
            return;
        }

        // Llenar inventario de forma más eficiente
        $inventory = $tile->getInventory();
        $items = [];
        for ($i = 0; $i < 27; $i++) {
            $randomItem = AirdropManager::getAirdrop()->getRandomItems();
            if ($randomItem !== null) {
                $items[$i] = $randomItem;
            }
        }
        $inventory->setContents($items);
        $tile->setName("§l§3Airdrop");

        // Actualizar item en mano
        $item->pop();
        $player->getInventory()->setItemInHand($item);

        // Texto flotante
        $TextPosition = $chestPos->add(0.5, 1.5, 0.5);
        $floatingText = new FloatingTextParticle("§l§3Airdrop", "");
        $world->addParticle($TextPosition, $floatingText);

        // Programar limpieza del texto
        Loader::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(
            function () use ($world, $TextPosition, $floatingText): void {
                $floatingText->setText("");
                $floatingText->setTitle("");
                $world->addParticle($TextPosition, $floatingText);
            }
        ), 120);

        $event->cancel();
    }
}