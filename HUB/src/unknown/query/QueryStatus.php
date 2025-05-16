<?php

namespace unknown\query;

class QueryStatus {

    public function __construct(
        public string $ip,
        public int $port
    ) {}

    public function query(): ?array {
        $socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        if (!$socket) {
            return null;
        }

        socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, ["sec"=>1,"usec"=>500000]);

        $pk = "\x01\x00\x00\x00\x00\x00\x00\x00";
        socket_sendto($socket, $pk, strlen($pk), 0, $this->ip, $this->port);

        $buffer = '';
        $from = '';
        $port = 0;

        if (@socket_recvfrom($socket, $buffer, 2048, 0, $from, $port) === false) {
            socket_close($socket);
            return null;
        }

        socket_close($socket);

        if (strlen($buffer) < 35) {
            return null;
        }

        $data = explode(';', substr($buffer, 35));
        return [
            "motd" => $data[1] ?? "Unknown",
            "online" => (int)($data[4] ?? 0),
            "max" => (int)($data[5] ?? 0),
        ];
    }
}
