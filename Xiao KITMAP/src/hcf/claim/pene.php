    # Chupa pija si lo lees 
    public function hadleItemInterect(PlayerBucketEmptyEvent $event): void
    {
        /** @var Player $player */
        $player = $event->getPlayer();
        $block = $event->getBlockClicked();
        $claim = HCFLoader::getInstance()->getClaimManager()->insideClaim($block->getPosition());

        if ($event->isCancelled())
            return;

        if ($player->isGod())
            return;

        if ($claim === null) {
            if ($block->getPosition()->distance($player->getServer()->getWorldManager()->getDefaultWorld()->getSafeSpawn()->asVector3()) < 400)
                $event->cancel();
            return;
        }
        if ($player->getInventory()->getItemInHand()->equals(VanillaItems::WATER_BUCKET(), false, false)) {
                $event->cancel();
            $player->sendMessage(TextFormat::colorize('§8[§4!§8] §7You cannot place blocks in this area'));
            return;
        }

        if (in_array($claim->getType(), ['spawn', 'road', 'koth', 'citadel'])) {
            $event->cancel();
            $player->sendMessage(TextFormat::colorize('&cYou cannot place blocks in this area'));
            return;
        }

    }
