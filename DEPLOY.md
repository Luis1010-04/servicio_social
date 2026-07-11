# ============================================================================
# DEPLOY.md — Guía de Despliegue con Docker
# IoT_Project — Sistema de Monitoreo IoT
# ============================================================================

## Requisitos Previos

### Hardware del servidor
- **CPU:** 2 cores mínimo
- **RAM:** 4GB mínimo
- **Disco:** 20GB mínimo
- **Red:** Conexión a LAN interna (para dispositivos ESP32)

### Software requerido
```bash
# Docker Engine 24+
docker --version

# Docker Compose v2+
docker compose version

# Git 2.0+
git --version
```

---

## Instalación Rápida

### 1. Clonar el repositorio
```bash
git clone <URL_DEL_REPOSITORIO> iot-project
cd iot-project
```

### 2. Configurar variables de entorno
```bash
# Copiar el archivo de ejemplo
cp .env.example .env

# Generar APP_KEY
docker compose run --rm app php artisan key:generate
```

### 3. Editar .env (Opcional)
```bash
# Editar con tu editor favorito
nano .env

# Cambiar valores importantes:
# - APP_URL=http://TU_IP_O_DOMINIO
# - DB_PASSWORD=una_contraseña_segura
# - DOCKER_INFLUXDB_INIT_PASSWORD=una_contraseña_segura
# - DOCKER_INFLUXDB_INIT_ADMIN_TOKEN=un_token_seguro
```

### 4. Levantar el stack
```bash
# Modo producción (recomendado)
docker compose -f docker-compose.yml -f docker-compose.prod.yml up -d

# O modo desarrollo (con hot-reload)
docker compose up -d
```

### 5. Verificar que todo funcione
```bash
# Ver estado de servicios
docker compose ps

# Ver logs
docker compose logs -f

# Probar la aplicación
curl http://localhost
```

---

## Credenciales por Defecto

| Servicio | Usuario | Contraseña |
|----------|---------|------------|
| **Laravel (Admin)** | admin@admin.com | admin |
| **MySQL** | root | root |
| **InfluxDB** | admin | changeme |

> ⚠️ **IMPORTANTE:** Cambiar todas las contraseñas en producción.

---

## Comandos Útiles

### Gestión de servicios
```bash
# Iniciar todos los servicios
docker compose up -d

# Detener todos los servicios
docker compose down

# Detener y eliminar volúmenes (⚠️ ELIMINA DATOS)
docker compose down -v

# Reconstruir imágenes
docker compose up -d --build

# Ver logs en tiempo real
docker compose logs -f

# Ver logs de un servicio específico
docker compose logs -f app
docker compose logs -f mysql
```

### Laravel
```bash
# Ejecutar Artisan
docker compose exec app php artisan

# Migrar base de datos
docker compose exec app php artisan migrate

# Ejecutar seeders
docker compose exec app php artisan db:seed

# Limpiar cache
docker compose exec app php artisan cache:clear
docker compose exec app php artisan config:clear
```

### Base de datos
```bash
# Acceder a MySQL
docker compose exec mysql mysql -u root -p

# Acceder a InfluxDB
docker compose exec influxdb influx
```

### MQTT
```bash
# Probar broker MQTT
mosquitto_sub -h localhost -t "test/#" -v

# Publicar mensaje de prueba
mosquitto_pub -h localhost -t "test" -m "Hello IoT"
```

---

## Puertos Expuertos

| Puerto | Servicio | Descripción |
|--------|----------|-------------|
| 80 | Nginx | Web (HTTP) |
| 1883 | Mosquitto | MQTT Broker |
| 8086 | InfluxDB | API InfluxDB |
| 3306 | MySQL | *Solo en desarrollo* |

---

## Persistencia de Datos

| Volumen | Contenido | Backup |
|---------|-----------|--------|
| `mysql_data` | Base de datos relacional | `docker compose exec mysql mysqldump -u root -p iot_project > backup.sql` |
| `influxdb_data` | Telemetría IoT | `docker compose exec influxdb influx backup /tmp/backup` |
| `mosquitto_data` | Estado MQTT | Copiar volumen |
| `storage_data` | Imágenes de perfil, exports | Copiar volumen |

---

## Firewall

### Puertos que deben estar abiertos

```bash
# HTTP (acceso web)
sudo ufw allow 80/tcp

# MQTT (dispositivos ESP32)
sudo ufw allow 1883/tcp

# InfluxDB (escritura directa desde ESP32)
sudo ufw allow 8086/tcp
```

### ⚠️ Restricciones de Seguridad

**Si el servidor tiene IP pública:**

```bash
# Bloquear puertos IoT desde internet
sudo ufw deny 1883/tcp from any
sudo ufw deny 8086/tcp from any

# Solo permitir desde LAN interna (ejemplo: 192.168.1.0/24)
sudo ufw allow from 192.168.1.0/24 to any port 1883
sudo ufw allow from 192.168.1.0/24 to any port 8086
```

---

## Solución de Problemas

### El contenedor `app` no inicia
```bash
# Ver logs de error
docker compose logs app

# Verificar que MySQL esté healthy
docker compose ps mysql
```

### Error de conexión a MySQL
```bash
# Verificar que MySQL esté corriendo
docker compose exec mysql mysqladmin ping

# Verificar credenciales en .env
grep DB_ .env
```

### InfluxDB no responde
```bash
# Verificar health
curl http://localhost:8086/health

# Ver logs
docker compose logs influxdb
```

### MQTT no conecta
```bash
# Verificar que Mosquitto esté corriendo
docker compose ps mosquitto

# Probar conexión
mosquitto_sub -h localhost -t "$$SYS/#" -C 1 -W 3
```

---

## Actualización

```bash
# Detener servicios
docker compose down

# Pull de imágenes actualizadas
docker compose pull

# Reconstruir y levantar
docker compose up -d --build

# Ejecutar migraciones si es necesario
docker compose exec app php artisan migrate --force
```

---

## Backup Completo

```bash
# Script de backup completo
#!/bin/bash
FECHA=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/backups/iot_project_$FECHA"

mkdir -p $BACKUP_DIR

# Backup MySQL
docker compose exec -T mysql mysqldump -u root -proot iot_project > $BACKUP_DIR/mysql.sql

# Backup InfluxDB
docker compose exec -T influxdb influx backup /tmp/influx_backup
docker cp iot_influxdb:/tmp/influx_backup $BACKUP_DIR/influx_backup

# Backup de archivos
cp -r storage/app $BACKUP_DIR/storage_app

echo "Backup completado en: $BACKUP_DIR"
```

---

## Soporte

Para problemas o dudas, consultar:
- `INSTALACION.md` — Instalación manual sin Docker
- `ARQUITECTURA.md` — Documentación técnica del sistema
- `README.md` — Información general del proyecto
