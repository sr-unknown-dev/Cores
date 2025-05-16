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

        // Timeout de recepción (1.5 segundos)
        socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, ["sec" => 1, "usec" => 500000]);

        // Paquete Ping estándar Bedrock (1 byte 0x01 + 8 bytes 0x00)
        $pk = "\x01" . str_repeat("\x00", 8);

        socket_sendto($socket, $pk, strlen($pk), 0, $this->ip, $this->port);

        $buffer = '';
        $from = '';
        $port = 0;

        $recv = @socket_recvfrom($socket, $buffer, 2048, 0, $from, $port);
        socket_close($socket);

        if ($recv === false || strlen($buffer) < 35) {
            return null;
        }

        // El payload útil empieza en el byte 35 (después de encabezados RakNet)
        $data = explode(';', substr($buffer, 35));

        return [
            "motd" => $data[1] ?? "Unknown",
            "online" => (int)($data[4] ?? 0),
            "max" => (int)($data[5] ?? 0),
        ];
    }
}
