<?php

declare(strict_types=1);

namespace hcf\handler\reclaim;

use hcf\player\Player;
use hcf\utils\serialize\Serialize;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\inventory\Inventory;
use pocketmine\item\Item;
use pocketmine\utils\TextFormat;

class Reclaim
{
    
    /**
     * Reclaim construct.
     * @param string $name
     * @param string $permission
     * @param int $time
     * @param Item[] $contents
     */
    public function __construct(
        private string $name,
        private string $permission,
        private int $time,
        private array $contents = []
    ) {}
    
    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
    
    /**
     * @return string
     */
    public function getPermission(): string
    {
        return $this->permission;
    }
    
    /**
     * @return int
     */
    public function getTime(): int
    {
        return $this->time;
    }
    
    /**
     * @return Item[]
     */
    public function getContents(): array
    {
        return $this->contents;
    }
    
    /**
     * @param Item[] $contents
     */
    public function setContents(array $contents): void
    {
        $this->contents = $contents;
    }
    
    /**
     * @param Player $player
     */
    public function giveContent(Player $player): void
    {
        foreach ($this->getContents() as $item) {
            if ($player->getInventory()->canAddItem($item)) {
                $player->getInventory()->addItem($item);
            } else {
                $player->dropItem($item);
            }
        }
    }
    
    /**
     * @return array
     */
    public function getData(): array
    {
        $data = [
            'permission' => $this->permission,
            'time' => $this->time,
            'contents' => []
        ];
        
        foreach ($this->contents as $item)
            $data['contents'][] = Serialize::serialize($item);
        return $data;
    }

    public function editContent(Player $player): void {
        $menu = InvMenu::create(InvMenuTypeIds::TYPE_CHEST);
        $menu->getInventory()->setContents($this->contents);
        $menu->setInventoryCloseListener(function (Player $player, Inventory $inventory): void {
            $this->contents = $inventory->getContents();
            $this->setContents($this->contents);
            $player->sendMessage(TextFormat::colorize('&aYou have edited the reclaim loot.'));
        });
        $menu->send($player, TextFormat::colorize('&2Reclaim edit'));
    }
}