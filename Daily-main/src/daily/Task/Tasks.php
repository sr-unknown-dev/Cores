<?php

namespace daily\Task;

use daily\Loader;
use daily\Utils\Utils;
use pocketmine\scheduler\Task;

class Tasks extends Task {
    private $main;

    public function __construct(Loader $main) {
        $this->main = $main;
    }

    public function onRun(): void {
        $config = Utils::getConfig();
        $allData = $config->getAll();
        $updated = false;

        foreach ($allData as $name => $data) {
            if (is_array($data) && isset($data["time"])) {
                if ($data["time"] < 432000) {
                    $data["time"] += 1;
                    $config->set($name, $data);
                    $updated = true;
                }
            } elseif (is_numeric($data)) {
                if ($data < 432000) {
                    $config->set($name, $data + 1);
                    $updated = true;
                }
            }
        }

        if ($updated) {
            $config->save();
        }
    }
}