<?php

declare(strict_types=1);

namespace hcf\handler\crate;

use hcf\utils\serialize\Serialize;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class Crate
{
    
    /** @var string */
    private string $name;

    private Item $keyId;
    /** @var string */
	private string $keyFormat;
	/** @var string */
	private string $nameFormat;
		/** @var string */
	private string $color;
	/** @var Item[] */
    private array $items;
    
    /** @var array */
    public array $floatingTexts = [];
    /** @var array */
    public array $floatingItems = [];
    /** @var array */
    public array $itemEntities = [];

    /**
     * Crate construct.
     * @param string $name
     * @param string $keyId
     * @param string $keyFormat
     * @param string $nameFormat
     * @param Item[] $items
     */
    public function __construct(string $name, Item $keyId, string $keyFormat, string $color, string $nameFormat, array $items)
    {
        $this->name = $name;
        $this->keyId = $keyId;
        $this->keyFormat = $keyFormat;
        $this->nameFormat = $nameFormat;
        $this->items = $items;
        $this->color = $color;
    }
    
    /**
	 * @return string
	 */
    public function getName(): string
    {
        return $this->name;
    }
    
	public function getKeyId(): Item
	{
		return $this->keyId;
	}
    
    /**
	 * @return string
	 */
	public function getKeyFormat(): string
	{
		return $this->keyFormat;
	}
	
	/**
	 * @return string
	 */
	public function getNameFormat(): string
	{
		return $this->nameFormat;
	}
	
	/**
	 * @return Item[]
	 */
	public function getItems(): array
    {
        return $this->items;
    }
    
    /**
	 * @return Item[]
	 */
	public function getColor(): string
    {
        return $this->color;
    }
    
    /**
	 * @param string $keyId
	 */
	public function setKeyId(Item $keyId): void
	{
		$this->keyId = $keyId;
	}
	
	/**
	 * @param string $keyFormat
	 */
	public function setKeyFormat(string $keyFormat): void
	{
		$this->keyFormat = $keyFormat;
	}
	
	/**
	 * @param string $nameFormat
	 */
	public function setNameFormat(string $nameFormat): void
	{
		$this->nameFormat = $nameFormat;
	}
	
	/**
	 * @param Item[] $items
	 */
	public function setItems(array $items): void
    {
        $this->items = $items;
    }
    
    /**
	 * @param string $nameFormat
	 */
	public function setColor(string $color): void
	{
		$this->color = $color;
	}
    
    /**
     * @param Player $player
     * @param int $count
     * @return bool
     */
    public function giveKey(Player $player, int $count = 1): bool
    {
        $item = $this->getKeyId();
        $item->setCustomName(TextFormat::colorize($this->getKeyFormat()));
        $item->setCount($count);
        $item->setLore([
            TextFormat::GRAY . 'You can redeem this key at crate',
			TextFormat::GRAY . 'in the spawn area.',
			'',
			TextFormat::GRAY . TextFormat::ITALIC . 'Left click to view crate rewards.',
			TextFormat::GRAY . TextFormat::ITALIC . 'Right click to open the crate.',
        ]);
        $item->setNamedTag($item->getNamedTag()->setString('crate_name', $this->getName()));
        
        if (!$player->getInventory()->canAddItem($item))
            return false;
        $player->getInventory()->addItem($item);
        return true;
    }
    
    /**
     * @param Player $player
     * @return bool
     */
    public function giveReward(Player $player): bool
    {
        $items = $this->getItems();
        $randomItem = $items[array_rand($items)];
        
        if (!$player->getInventory()->canAddItem($randomItem))
            return false;
        $itemInHand = $player->getInventory()->getItemInHand();
        
        if($itemInHand->getCount() > 1){
            $itemInHand->setCount($itemInHand->getCount() - 1);
        }else{
            $itemInHand = VanillaItems::AIR();
        }
        $player->getInventory()->setItemInHand($itemInHand);
        $player->getInventory()->addItem($randomItem);
        return true;
    }
    
    /**
     * @return array
     */
    public function getData(): array
    {
        $data = [
            'key' => Serialize::serialize($this->getKeyId()),
            'keyFormat' => $this->getKeyFormat(),
            'color' => $this->getColor(),
            'nameFormat' => $this->getNameFormat(),
            'items' => [],
            'color' => $this->getColor()
        ];
        
        foreach ($this->getItems() as $slot => $item) {
            $data['items'][$slot] = Serialize::serialize($item);
        }
        return $data;
    }
}