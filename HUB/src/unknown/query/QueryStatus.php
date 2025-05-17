<?php

declare(strict_types=1);

namespace unknown\query;

use pocketmine\scheduler\ClosureTask;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class QueryStatus {
    private string $host;
    private int $port;
    private array $cachedStatus = [];
    private int $cacheTime = 60; // Cache time in seconds
    private int $lastQuery = 0;

    /**
     * @param string $host The server IP address
     * @param int $port The server port (default: 19132)
     */
    public function __construct(string $host, int $port = 19132) {
        $this->host = $host;
        $this->port = $port;
    }

    /**
     * Query the server status
     *
     * @return array The server status information
     */
    public function query(): array {
        $currentTime = time();

        // Return cached status if available and not expired
        if (!empty($this->cachedStatus) && ($currentTime - $this->lastQuery) < $this->cacheTime) {
            return $this->cachedStatus;
        }

        $socket = @fsockopen("udp://{$this->host}", $this->port, $errno, $errstr, 2);
        if (!$socket) {
            $this->cachedStatus = ["status" => "Off", "error" => "$errstr ($errno)"];
            $this->lastQuery = $currentTime;
            return $this->cachedStatus;
        }

        // Prepare and send query packet
        $timeStamp = microtime(true);
        $clientId = mt_rand(1, 0xFFFFFFFF);

        // Send handshake packet
        $handshakePacket = $this->writePacket(0x09, $clientId);
        fwrite($socket, $handshakePacket);
        $response = fread($socket, 4096);

        if (!$response) {
            fclose($socket);
            $this->cachedStatus = ["status" => "Off"];
            $this->lastQuery = $currentTime;
            return $this->cachedStatus;
        }

        // Send stat packet
        $statPacket = $this->writePacket(0x00, $clientId);
        fwrite($socket, $statPacket);
        $response = fread($socket, 4096);
        fclose($socket);

        if (!$response) {
            $this->cachedStatus = ["status" => "Off"];
            $this->lastQuery = $currentTime;
            return $this->cachedStatus;
        }

        // Parse response
        $serverInfo = $this->parseResponse($response);

        $this->cachedStatus = [
            "status" => "On",
            "players_online" => $serverInfo["numplayers"] ?? 0,
            "max_players" => $serverInfo["maxplayers"] ?? 0,
            "server_ip" => $this->host,
            "server_name" => $serverInfo["hostname"] ?? "Unknown",
            "version" => $serverInfo["version"] ?? "Unknown"
        ];

        $this->lastQuery = $currentTime;
        return $this->cachedStatus;
    }

    /**
     * Write a query packet
     *
     * @param int $type Packet type
     * @param int $sessionId Session ID
     * @return string The packet
     */
    private function writePacket(int $type, int $sessionId): string {
        $buffer = "";
        $buffer .= "\xFE\xFD";
        $buffer .= chr($type);
        $buffer .= pack("N", $sessionId);

        return $buffer;
    }

    /**
     * Parse the query response
     *
     * @param string $response The server response
     * @return array The parsed data
     */
    private function parseResponse(string $response): array {
        $data = [];

        // Skip header (5 bytes)
        $response = substr($response, 5);

        // Split data by null byte
        $parts = explode("\x00", $response);

        // Parse key-value pairs
        for ($i = 0; $i < count($parts) - 1; $i += 2) {
            if (!empty($parts[$i])) {
                $data[$parts[$i]] = $parts[$i + 1] ?? '';
            }
        }

        return $data;
    }

    /**
     * Set the cache time
     *
     * @param int $seconds Cache time in seconds
     * @return self
     */
    public function setCacheTime(int $seconds): self {
        $this->cacheTime = $seconds;
        return $this;
    }

    /**
     * Schedule periodic status updates
     *
     * @param \pocketmine\plugin\Plugin $plugin The plugin instance
     * @param int $intervalTicks Update interval in ticks
     * @return void
     */
    public function scheduleUpdates($plugin, int $intervalTicks = 1200): void {
        $plugin->getScheduler()->scheduleRepeatingTask(new ClosureTask(
            function() {
                $this->query(); // Update cache
            }
        ), $intervalTicks);
    }

    /**
     * Get formatted status text
     *
     * @return string Formatted status text
     */
    public function getFormattedStatus(): string {
        $status = $this->query();

        if ($status["status"] === "Off") {
            return TextFormat::RED . "Offline";
        }

        return TextFormat::GREEN . "Online " . TextFormat::WHITE . "(" .
               TextFormat::YELLOW . $status["players_online"] . TextFormat::WHITE . "/" .
               TextFormat::YELLOW . $status["max_players"] . TextFormat::WHITE . ")";
    }
}