<?php

namespace hcf\databases;

use hcf\Loader;
use mysqli;

class Database {
    private static ?Database $instance = null;
    private mysqli $connection;

    public function __construct() {
        $config = Loader::getInstance()->getConfig();

        try {
            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
            
            $this->connection = new mysqli(
                $config->getNested("database.host"),
                $config->getNested("database.username"),
                $config->getNested("database.password"),
                $config->getNested("database.database"),
                $config->getNested("database.port")
            );

            $this->connection->set_charset('utf8mb4');
            $this->connection->options(MYSQLI_OPT_CONNECT_TIMEOUT, 10);
            
            if ($this->connection->connect_error) {
                throw new \Exception("Connection failed: " . $this->connection->connect_error);
            }

            $this->init();
        } catch (\Exception $e) {
            Loader::getInstance()->getLogger()->critical("Database connection error: " . $e->getMessage());
            Loader::getInstance()->getServer()->shutdown();
        }
    }

    private function init(): void {
        try {
            $this->connection->query("CREATE TABLE IF NOT EXISTS player_ranks (
                player_name VARCHAR(32) PRIMARY KEY,
                rank_name VARCHAR(32),
                expiration_time INT DEFAULT 0
            )");
        } catch (\Exception $e) {
            Loader::getInstance()->getLogger()->critical("Database initialization error: " . $e->getMessage());
            Loader::getInstance()->getServer()->shutdown();
        }
    }

    public static function getInstance(): Database {
        if(self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection(): mysqli {
        if (!$this->connection->ping()) {
            $this->__construct();
        }
        return $this->connection;
    }
}