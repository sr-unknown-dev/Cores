<?php

namespace hcf\module;

use hcf\module\announcement\AnnouncementManager;
use hcf\module\blockshop\BlockShopManager;
use hcf\module\coinshop\CoinShopManager;
use hcf\module\NPC\NPCManager;
//use hcf\module\coinshop\CoinShopManager;
use hcf\module\lootbox\LootBoxManager;
use hcf\module\compass\CompassManager;
use hcf\module\rollback\RollbackManager;
use hcf\module\treasureisland\TreasureIslandManager;

class ModuleManager {
    
    public CompassManager $compassManager;
    public AnnouncementManager $announcementManager;
    public TreasureIslandManager $treasureIslandManager;
    //public CoinShopManager $coinshopsanager;
    public BlockShopManager $blockShopManager;
    public function __construct(){
        $this->announcementManager = new AnnouncementManager;
        $this->blockShopManager = new BlockShopManager;
        $this->compassManager = new CompassManager;
        $this->treasureIslandManager = new TreasureIslandManager;
       // $this->coinshopsanager = new CoinShopManager;
    }
    

    public function getAnnouncementManager(): AnnouncementManager {
        return $this->announcementManager;
    }

    public function getBlockShopManager(): BlockShopManager {
        return $this->blockShopManager;
    }
    
    public function getCompassManager(): CompassManager {
        return $this->compassManager;
    }

    public function getTreasureIslandManager(): TreasureIslandManager {
        return $this->treasureIslandManager;
    }

    /**public function CoinShopManager(): CoinShopManager {
        return $this->coinshopsanager;
    }*/

}