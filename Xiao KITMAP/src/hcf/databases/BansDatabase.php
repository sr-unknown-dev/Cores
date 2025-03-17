<?php

namespace hcf\databases;

use hcf\Loader;
use mysqli;

class BansDatabase {
    private static ?BansDatabase $instance = null;
    private mysqli $connection;

    public function __construct() {
        $config = Loader::getInstance()->getConfig();
        $this->connection = new mysqli(
            $config->getNested("database.host"),
            $config->getNested("database.username"),
            $config->getNested("database.password"),
            $config->getNested("database.database"),
            $config->getNested("database.port")
        );

        $this->connection->query("CREATE TABLE IF NOT EXISTS bans (
            player_name VARCHAR(32) PRIMARY KEY,
            reason VARCHAR(255),
            banned_by VARCHAR(32),
            expiration_time INT DEFAULT 0
        )");
    }

    public static function getInstance(): BansDatabase {
        if(self::$instance === null) {
            self::$instance = new BansDatabase();
        }
        return self::$instance;
    }

    public function getConnection(): mysqli {
        return $this->connection;
    }
}