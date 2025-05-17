<?php

namespace unknown\query;

class QueryStatus {
    private $host;
    private $port;

    public function __construct($host, $port = 19132) {
        $this->host = $host;
        $this->port = $port;
    }

    public function query() {
        $socket = fsockopen("udp://{$this->host}", $this->port, $errno, $errstr, 2);
        if (!$socket) {
            return ["status" => "Off", "error" => "$errstr ($errno)"];
        }

        $packet = "\xFE\xFD\x09\x10\x20\x30\x40";
        fwrite($socket, $packet);
        $response = fread($socket, 4096);
        fclose($socket);

        if (!$response) {
            return ["status" => "Off"];
        }

        $data = unpack("C*", $response);
        return [
            "status" => "On",
            "players_online" => $data[5] ?? 0,
            "max_players" => $data[6] ?? 0,
            "server_ip" => $this->host
        ];
    }
}