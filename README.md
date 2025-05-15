# HCF Core & KitMap - Sistema Completo de Facciones

Un sistema completo que combina HCF (Hardcore Factions) y KitMap, desarrollado para PocketMine-MP, ofreciendo una
experiencia PvP competitiva y emocionante.

## 📋 Características Principales

### Sistema KitMap

- **Kits Personalizables**
  - Kits por rangos/permisos
  - Cooldowns configurables
  - Previsualización de kits
  - Sistema de guardado de kits personalizados

- **Sistema de Combate**
  - Anti-Logout
  - Combat-Tag configurable
  - Pearl cooldown
  - Sistema de killstreaks

- **Scoreboard Dinámico**
  - Estadísticas en tiempo real
  - Killstreak actual
  - Tiempo restante de combate
  - FPS y Ping del jugador

### Sistema de Airdrop

- Caída de suministros aleatorios
- Efectos visuales y sonoros
- Sistema de texto flotante
- Items configurables
- Optimizado para alto rendimiento

### Sistema AutoFix

- Reparación automática de equipamiento
- Sistema optimizado contra lag
- Intervalos configurables
- Reparación inteligente

## ⚙️ Configuración

## 📌 Comandos

### Comandos de KitMap

```
/kit [nombre] - Selecciona un kit
/kits - Muestra todos los kits disponibles
```

### Comandos de Administración

```
/airdrop give [jugador] [cantidad] - Da airdrops
/fix auto - Activa/desactiva el auto fix
```

## 📊 Características de Estadísticas

- **Estadísticas Guardadas**
  - Kills
  - Deaths
  - KDR
  - Mejor killstreak
  - Tiempo jugado
  - Airdrops abiertos
  - Items reparados

- **Sistema de Leaderboards**
  - Top kills
  - Top KDR
  - Mejor killstreak
  - Rankings semanales/mensuales

## ⚡ Optimizaciones

### Rendimiento

- Cache de datos
- Procesamiento por lotes
- Reducción de operaciones de disco
- Gestión eficiente de memoria
- Sistema de cola para tareas pesadas

### Anti-Lag

- Limitadores de partículas
- Control de spawn de entidades
- Optimización de inventarios
- Limpieza automática de items

## 🔄 Sistemas de Eventos

### Eventos Automáticos

- KOTH
- LMS (Last Man Standing)
- Torneos automáticos
- Eventos especiales programados

## 📱 Interfaz y Menús

- **GUI Intuitiva**
  - Selector de kits
  - Previsualizador de contenido
  - Menú de estadísticas
  - Configurador de loadouts

## 🛠️ Instalación

1. Descarga el plugin
2. Colócalo en la carpeta `plugins`
3. Configura los archivos:
  - `config.yml`
  - `kits.yml`
  - `messages.yml`
  - `scoreboard.yml`
4. Reinicia el servidor

## 🔄 Actualizaciones Futuras

- [ ] Sistema de clanes
- [ ] Eventos personalizados
- [ ] Sistema de economía integrado
- [ ] Más kits y variaciones
- [ ] Sistema de rangos por kills
- [ ] Torneos automatizados
- [ ] Sistema de recompensas diarias

## 🐛 Reporte de Bugs

Para reportar bugs, incluye:

1. Descripción del error
2. Pasos para reproducir
3. Versión del servidor/plugin
4. Logs relevantes
5. Configuración actual

## 📝 Notas de Desarrollo

- Código modular y extensible
- Documentación completa
- Patrones de diseño optimizados
- Sistema de eventos personalizado
- API para desarrolladores

## 🤝 Contribuciones

¡Las contribuciones son bienvenidas!

1. Fork del proyecto
2. Crea tu rama (`git checkout -b feature/NuevaCaracteristica`)
3. Commit (`git commit -m 'Añadida nueva característica'`)
4. Push (`git push origin feature/NuevaCaracteristica`)
5. Abre un Pull Request

## 📜 Licencia

Este proyecto está bajo la licencia MIT. Ver archivo `LICENSE`.

---

Para soporte o preguntas:

- Abre un issue en GitHub
- Discord: [Enlace al servidor de soporte]
- Email: [email de soporte]

¡Gracias por usar nuestro plugin! Esperamos que mejore la experiencia de tu servidor.