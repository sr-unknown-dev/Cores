<?php

namespace unknown\query;

use pocketmine\utils\InternetAddress;
use pocketmine\network\mcpe\query\QueryPacket;
use unknown\Loader;

class QueryManager {

    /** @var QueryStatus[] */
    private array $servers = [];

    /** @var array<string, array> */
    private array $cache = [];

    public function __construct() {
        $configServers = Loader::getInstance()->getConfig()->get('servers') ?? [];

        foreach ($configServers as $name => $info) {
            $ip = $info['ip'] ?? '127.0.0.1';
            $port = $info['port'] ?? 19132;

            $this->servers[$name] = new QueryStatus($ip, (int)$port);
        }
    }

    public function update(): void {
        foreach ($this->servers as $name => $server) {
            $info = $server->query();
            $this->cache[$name] = $info ?? ["motd" => "Offline", "online" => 0, "max" => 0];
        }
    }

    public function getStatus(string $name): ?array {
        return $this->cache[$name] ?? null;
    }
}
