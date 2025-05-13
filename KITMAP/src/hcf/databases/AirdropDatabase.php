<?php

namespace hcf\databases;

use hcf\Loader;
use mysqli;

class AirdropDatabase {
    private static ?AirdropDatabase $instance = null;
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

        $this->connection->query("CREATE TABLE IF NOT EXISTS airdrops (
            id INT AUTO_INCREMENT PRIMARY KEY,
            items LONGTEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
    }

    public static function getInstance(): AirdropDatabase {
        if(self::$instance === null) {
            self::$instance = new AirdropDatabase();
        }
        return self::$instance;
    }

    public function getConnection(): mysqli {
        return $this->connection;
    }
}