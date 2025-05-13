<?php

namespace hcf\handler;

use hcf\handler\airdrop\AirdropManager;
use hcf\handler\crate\CrateManager;
use hcf\handler\kit\KitManager;
use hcf\handler\kit\op\KitManagerOp;
use hcf\handler\kit\pay\KitManagerPay;
use hcf\handler\KnockBack\KnockBackManager;
use hcf\handler\package\PackageManager;
use hcf\handler\prefix\manager\PrefixManager;
use hcf\handler\reclaim\ReclaimManager;
use hcf\handler\rollback\RollbackListener;
use hcf\handler\rollback\RollbackManager;
use hcf\Loader;

class HandlerManager {

    public KitManager $kitManager;
    public CrateManager $crateManager;
    public KitManagerPay $kitManagerPay;
    public PackageManager $packageManager;

    public AirdropManager $airdropManager;
    public ReclaimManager $reclaimManager;
    public KitManagerOp $kitManagerOp;
    public PrefixManager $prefixManager;

    public function __construct(){
        $this->kitManager = new KitManager;
        $this->crateManager = new CrateManager;
        $this->kitManagerPay = new KitManagerPay;
        $this->kitManagerOp = new KitManagerOp;
        $this->packageManager = new PackageManager;
        $this->airdropManager = new AirdropManager;
        $this->reclaimManager = new ReclaimManager;
        $this->prefixManager = new PrefixManager;
    }

    /**
     * @return PrefixManager
     */
    public function getPrefixManager(): PrefixManager
    {
        return $this->prefixManager;
    }

    public function getKitManager(): KitManager {
        return $this->kitManager;
    }

    public function getCrateManager(): CrateManager {
        return $this->crateManager;
    }

    public function getKitPayManager(): KitManagerPay {
        return $this->kitManagerPay;
    }

    public function getKitOpManager(): KitManagerOp {
        return $this->kitManagerOp;
    }

    public function getPackageManager(): PackageManager {
        return $this->packageManager;
    }

    public function getAirdropManager(): AirdropManager {
        return $this->airdropManager;
    }

    public function getReclaimManager(): ReclaimManager {
        return $this->reclaimManager;
    }

}