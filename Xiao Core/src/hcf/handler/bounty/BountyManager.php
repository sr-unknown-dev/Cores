<?php

namespace hcf\handler\bounty;

use hcf\Loader;
use hcf\player\Player;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

class BountyManager {
    private Config $bounties;

    public function __construct() {
        $this->bounties = new Config(Loader::getInstance()->getDataFolder(). "bounties.json", Config::JSON);
    }

    /**
     * @param string $target
     * @param string $player
     * @param int $amount
     * @return void
     * @throws \JsonException
     */
    public function setBounty(string $target, string $player, int $amount) {
        if ($this->bounties->exists($target)) return;
        $data = [
            "Player" => $player,
            "Amount" => $amount
        ];
        $this->bounties->set($target, $data);
        $this->bounties->save();
    }

    /**
     * @param string $target
     * @return void
     * @throws \JsonException
     */
    public function removeBounty(string $target)
    {
        if (!$this->bounties->exists($target)) return;

        $this->bounties->remove($target);
        $this->bounties->save();
    }

    public function getBounty(string $player){
    }

    /**
     * @param Player $target
     * @param Player $killer
     * @return void
     */
    public function claimBounty(Player $target, Player $killer) {
        $player = $this->bounties->get($target->getName())["Player"];
        if (!$this->bounties->exists($target->getName())) return;

        if ($player instanceof Player){
            $player->getSession()->setBalance($target->getSession()->getBalance() - $this->bounties->get($target->getName())["Amount"]);
            $killer->getSession()->setBalance($killer->getSession()->getBalance() - $this->bounties->get($target->getName())["Amount"]);
            $killer->sendMessage(TextFormat::colorize("&aYou have received &g".$this->bounties->get($target->getName())["Amount"]." &abounty for killing &g".$target->getName()));
            $this->removeBounty($target->getName());
        }
    }

    /**
     * @param string $target
     * @return bool
     */
    public function hasBounty(string $target):bool{
        if (!$this->bounties->exists($target)){
            return false;
        }
        return true;
    }
}