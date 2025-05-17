// En tu plugin principal
use unknown\query\QueryStatus;

// Crear una instancia para consultar un servidor
$queryStatus = new QueryStatus("play.example.com", 19132);

// Establecer tiempo de caché (opcional)
$queryStatus->setCacheTime(30); // 30 segundos

// Programar actualizaciones automáticas (opcional)
$queryStatus->scheduleUpdates($this, 1200); // Cada 1200 ticks (1 minuto)

// Obtener el estado del servidor
$serverStatus = $queryStatus->query();

// Verificar si el servidor está en línea
if ($serverStatus["status"] === "On") {
    $playersOnline = $serverStatus["players_online"];
    $maxPlayers = $serverStatus["max_players"];
    $serverName = $serverStatus["server_name"];
    
    // Hacer algo con la información...
    $this->getLogger()->info("Servidor: $serverName - Jugadores: $playersOnline/$maxPlayers");
} else {
    $this->getLogger()->warning("El servidor está offline");
}

// O simplemente obtener un texto formateado
$statusText = $queryStatus->getFormattedStatus();