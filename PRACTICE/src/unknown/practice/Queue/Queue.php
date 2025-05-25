<?php

namespace unknown\practice\Queue;

class Queue
{
    public string $mode;
    public string $type;
    public string $owner;

    // STATES: PENDING, PAIRED, IN
    public string $state = "PENDING";

    public function __construct(string $mode, string $type, string $owner, string $state = "PENDING")
    {
        $this->mode = $mode;
        $this->type = $type;
        $this->owner = $owner;
        $this->state = $state;
    }

    public function getMode(): string
    {
        return $this->mode;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getOwner(): string
    {
        return $this->owner;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function setMode(string $mode): void
    {
        $this->mode = $mode;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function setOwner(string $owner): void
    {
        $this->owner = $owner;
    }

    public function setState(string $state): void
    {
        $this->state = $state;
    }
}
