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

### Configuración de KitMap

```yaml
kitmap:
  # Configuración general
  settings:
    combat-tag: 30
    pearl-cooldown: 15
    killstreak-rewards: true

  # Configuración de kits
  kits:
    pvp:
      cooldown: 3600
      items:
        - "diamond_sword:1:sharpness:3"
        - "diamond_helmet:1:protection:3"
        # ... más items

    archer:
      cooldown: 7200
      permission: "kit.archer"
      items:
        - "bow:1:power:3"
        - "leather_helmet:1:protection:2"
        # ... más items

  # Configuración de killstreaks
  killstreaks:
    5:
      - "effect:strength:30:1"
    10:
      - "command:give {player} golden_apple 5"
```

### Configuración de Scoreboard

```yaml
scoreboard:
  title: "§l§6KITMAP"
  lines:
    - "§7Jugador: §f{player}"
    - "§7Kills: §a{kills}"
    - "§7Deaths: §c{deaths}"
    - "§7KDR: §e{kdr}"
    - "§7Killstreak: §6{killstreak}"
    - ""
    - "§7Combat: §c{combat}"
    - "§7Online: §a{online}"
    - "§7FPS: §e{fps}"
```

## 📌 Comandos

### Comandos de KitMap

```
/kit [nombre] - Selecciona un kit
/kits - Muestra todos los kits disponibles
/createkit [nombre] - Crea un nuevo kit
/savekit [nombre] - Guarda tu kit actual
/killstreak - Muestra tu racha actual
/stats [jugador] - Muestra estadísticas
```

### Comandos de Administración

```
/kitmap reload - Recarga la configuración
/setspawn - Establece el punto de spawn
/setwarp [nombre] - Crea un nuevo warp
/airdrop give [jugador] [cantidad] - Da airdrops
/autofix toggle - Activa/desactiva el auto fix
```

## 🔒 Permisos

```yaml
permisos:
  kitmap.kit.*:
    descripción: Acceso a todos los kits
    default: op
  kitmap.kit.vip:
    descripción: Acceso a kits VIP
    default: false
  kitmap.admin:
    descripción: Comandos administrativos
    default: op
  hcf.airdrop.give:
    descripción: Dar airdrops
    default: op
  hcf.autofix:
    descripción: Usar auto fix
    default: op
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
