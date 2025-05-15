<?php

namespace unknown\query;

class QueryStatus {

    public function __construct(
        public string $ip,
        public int $port
    ) {}

    public function query(): ?array {
        $socket = @fsockopen($this->ip, $this->port, $errno, $errstr, 1.5);

        if (!$socket) {
            return null; // Servidor offline
        }

        fwrite($socket, "\xFE");
        $data = fread($socket, 512);
        fclose($socket);

        if ($data === false || substr($data, 0, 1) !== "\xFF") {
            return null;
        }

        $data = substr($data, 3);
        $data = iconv("UTF-16BE", "UTF-8", $data);
        $data = explode("§", $data);

        return [
            "motd" => $data[0] ?? "Unknown",
            "online" => (int)($data[1] ?? 0),
            "max" => (int)($data[2] ?? 0)
        ];
    }
}
