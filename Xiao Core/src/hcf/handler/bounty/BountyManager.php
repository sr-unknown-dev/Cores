<?php

namespace hcf\handler\bounty;

use hcf\handler\bounty\commands\BountyCommand;
use hcf\handler\bounty\commands\SetBountyCommand;
use hcf\Loader;
use hcf\player\Player;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

class BountyManager {
    private Config $bounties;
    public $trackedBounties;

    public function __construct() {
        $this->bounties = new Config(Loader::getInstance()->getDataFolder(). "bountys.json", Config::JSON);
        Loader::getInstance()->getServer()->getCommandMap()->registerAll("bountys", [
            new BountyCommand(),
            new SetBountyCommand(),
        ]);
        Loader::getInstance()->getServer()->getPluginManager()->registerEvents(new BountyListener(), Loader::getInstance());
    }

    /**
     * @param string $target
     * @param string $player
     * @param int $amount
     * @return void
     * @throws \JsonException
     */
    public function addBounty(Player $target, string $player, int $amount) {
        if ($this->bounties->exists($target->getName())) return;
        $data = [
            "Player" => $player,
            "Amount" => $amount
        ];
        $this->bounties->set($target->getName(), $data);
        $this->bounties->save();
    }

    /**
     * @param string $target
     * @return void
     * @throws \JsonException
     */
    public function removeBounty(Player $target)
    {
        if (!$this->bounties->exists($target->getName())) return;

        $this->bounties->remove($target->getName());
        $this->bounties->save();
    }

    public function getBounty(string $player){
    }

    /**
     * @param Player $target
     * @param Player $killer
     * @return void
     */
    public function claimBounty(Player $target, Player $killer): void
    {
        $targetName = $target->getName();
        if (!$this->bounties->exists($targetName)) return;

        $bountyData = $this->bounties->get($targetName);
        $amount = $bountyData["Amount"];
        $killer->getSession()->setBalance($killer->getSession()->getBalance() + $amount);
        $killer->sendMessage(TextFormat::colorize("&aYou have received &g$" . $amount . " &abounty for killing &g" . $targetName));
        $this->removeBounty($target);
    }

    public function hasBounty(string $target): bool
    {
        return $this->bounties->exists($target);
    }

    public function trackBounty(Player $player, Player $target)
    {
        if (!$this->hasBounty($target->getName())) {
            $player->sendMessage(TextFormat::colorize("&cThis player doesn't have a bounty"));
            return;
        }

        $this->trackedBounties[$player->getName()] = [
            "target" => $target->getName(),
            "amount" => $this->bounties->get($target->getName())["Amount"]
        ];

        $player->sendMessage(TextFormat::colorize("&aYou are now tracking " . $target->getName()));
    }

    public function hasTrackedBounty(Player $player): bool {
        return isset($this->trackedBounties[$player->getName()]);
    }

    public function getTrackedBountyData(Player $player): ?array {
        return $this->trackedBounties[$player->getName()] ?? null;
    }

    public function getAllBountys(): array {
        $bounties = $this->bounties->getAll();
        $formattedBounties = [];

        foreach ($bounties as $target => $data) {
            if (isset($data["Player"]) && isset($data["Amount"])) {
                $formattedBounties[$target] = [
                    "Player" => $data["Player"],
                    "Amount" => $data["Amount"]
                ];
            }
        }

        return $formattedBounties;
    }
}