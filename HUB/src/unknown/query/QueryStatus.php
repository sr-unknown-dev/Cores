<?php

namespace unknown\query;

class QueryStatus {
    private $host;
    private $port;

    public function __construct($host, $port) {
        $this->host = $host;
        $this->port = $port;
    }

    public function getStatus() {
        $socket = @fsockopen($this->host, $this->port, $errno, $errstr, 2);

        if (!$socket) {
            return [
                'status' => 'offline',
                'players_online' => 0,
                'max_players' => 0
            ];
        }

        fwrite($socket, "\xFE\x01");
        $response = fread($socket, 1024);
        fclose($socket);

        if (!$response) {
            return [
                'status' => 'offline',
                'players_online' => 0,
                'max_players' => 0
            ];
        }

        $data = explode("\x00", mb_convert_encoding($response, 'UTF-8', 'UCS-2BE'));

        return [
            'status' => 'online',
            'players_online' => (int) $data[4],
            'max_players' => (int) $data[5]
        ];
    }
}