<?php

namespace UserDatabase; // Define el espacio de nombres para el plugin

use pocketmine\plugin\PluginBase; // Importa la clase base del plugin
use pocketmine\command\Command; // Importa la clase para comandos
use pocketmine\command\CommandSender; // Importa la clase para el emisor de comandos
use mysqli; // Importa la clase mysqli para manejar la conexión a MySQL

class UserDatabase extends PluginBase { // Define la clase principal del plugin que extiende PluginBase

    private $db; // Variable para almacenar la conexión a la base de datos

    public function onEnable() { // Método que se llama cuando el plugin se habilita
        // Conexión a la base de datos MySQL
        $this->db = new mysqli("HOST", "USERNAME", "PASSWORD", "DATABASE"); // Crea una nueva conexión a la base de datos
        if ($this->db->connect_error) { // Verifica si hay un error de conexión
            $this->getLogger()->error("Error de conexión: " . $this->db->connect_error); // Muestra el error en el log
            return; // Termina la ejecución si hay un error
        }
        // Crea la tabla 'users' si no existe
        $this->db->query("CREATE TABLE IF NOT EXISTS users (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(255), email VARCHAR(255))"); // Crea la tabla 'users'
    }

    public function onDisable() { // Método que se llama cuando el plugin se deshabilita
        $this->db->close(); // Cierra la conexión a la base de datos
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool { // Maneja los comandos enviados al servidor
        switch ($command->getName()) { // Verifica el nombre del comando
            case "adduser": // Comando para agregar un usuario
                if (count($args) < 3) { // Verifica que se hayan proporcionado suficientes argumentos
                    $sender->sendMessage("Uso: /adduser <nombre> <email>"); // Mensaje de uso correcto
                    return false; // Termina la ejecución del comando
                }
                $name = $args[1]; // Obtiene el nombre del usuario
                $email = $args[2]; // Obtiene el correo electrónico del usuario
                $this->addUser($name, $email); // Llama al método para agregar el usuario
                $sender->sendMessage("Usuario agregado: $name"); // Mensaje de confirmación
                return true; // Indica que el comando se ejecutó correctamente

            case "getuser": // Comando para obtener un usuario
                if (count($args) < 2) { // Verifica que se haya proporcionado un argumento
                    $sender->sendMessage("Uso: /getuser <nombre>"); // Mensaje de uso correcto
                    return false; // Termina la ejecución del comando
                }
                $name = $args[1]; // Obtiene el nombre del usuario
                $user = $this->getUser($name); // Llama al método para obtener el usuario
                if ($user) { // Verifica si se encontró el usuario
                    $sender->sendMessage("Usuario: $user[0], Email: $user[1]"); // Muestra la información del usuario
                } else {
                    $sender->sendMessage("Usuario no encontrado."); // Mensaje si no se encuentra el usuario
                }
                return true; // Indica que el comando se ejecutó correctamente

            case "deleteuser": // Comando para eliminar un usuario
                if (count($args) < 2) { // Verifica que se haya proporcionado un argumento
                    $sender->sendMessage("Uso: /deleteuser <nombre>"); // Mensaje de uso correcto
                    return false;
                }
                $name = $args[1]; // Obtiene el nombre del usuario
                $this->deleteUser ($name); // Llama al método para eliminar el usuario
                $sender->sendMessage("Usuario eliminado: $name"); // Mensaje de confirmación
                return true; // Indica que el comando se ejecutó correctamente
        }
        return false; // Si el comando no coincide, devuelve false
    }

    private function addUser (string $name, string $email): void { // Método para agregar un usuario a la base de datos
        $stmt = $this->db->prepare("INSERT INTO users (name, email) VALUES (?, ?)"); // Prepara la consulta SQL para insertar un nuevo usuario
        $stmt->bind_param("ss", $name, $email); // Vincula los parámetros a la consulta
        $stmt->execute(); // Ejecuta la consulta
        $stmt->close(); // Cierra la declaración
    }

    private function getUser (string $name): ?array { // Método para obtener un usuario de la base de datos
        $stmt = $this->db->prepare("SELECT name, email FROM users WHERE name = ?"); // Prepara la consulta SQL para seleccionar un usuario por nombre
        $stmt->bind_param("s", $name); // Vincula el parámetro a la consulta
        $stmt->execute(); // Ejecuta la consulta
        $result = $stmt->get_result(); // Obtiene el resultado de la consulta
        if ($result->num_rows > 0) { // Verifica si se encontró al menos un usuario
            return $result->fetch_array(MYSQLI_NUM); // Devuelve el usuario como un array
        }
        return null; // Si no se encuentra el usuario, devuelve null
    }

    private function deleteUser (string $name): void { // Método para eliminar un usuario de la base de datos
        $stmt = $this->db->prepare("DELETE FROM users WHERE name = ?"); // Prepara la consulta SQL para eliminar un usuario por nombre
        $stmt->bind_param("s", $name); // Vincula el parámetro a la consulta
        $stmt->execute(); // Ejecuta la consulta
        $stmt->close(); // Cierra la declaración
    }
}