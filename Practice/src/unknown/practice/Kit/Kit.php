<?php

namespace unknown\practice\Kit;

class Kit
{
    private string $name;
    private array $items;
    private array $armor;

    public function __construct(string $name, array $items, array $armor)
    {
        $this->name = $name;
        $this->items = $items;
        $this->armor = $armor;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function getArmor(): array
    {
        return $this->armor;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setItems(array $items): void
    {
        $this->items = $items;
    }

    public function setArmor(array $armor): void
    {
        $this->armor = $armor;
    }

    public function Data(): array
    {
        return [
            'name' => $this->name,
            'items' => $this->items,
            'armor' => $this->armor
        ];
    }
}
