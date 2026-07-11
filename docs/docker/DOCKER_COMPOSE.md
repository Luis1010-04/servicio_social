# Docker Compose — Servicios y Configuración

## Archivos de Compose

| Archivo | Propósito | Uso |
|---------|-----------|-----|
| `docker-compose.yml` | Configuración base (todos los servicios) | Siempre se carga |
| `docker-compose.override.yml` | Desarrollo local (bind mounts, debug) | Se carga automático con `docker compose up` |
| `docker-compose.prod.yml` | Producción (sin bind mounts, límites de recursos) | Usar con `-f docker-compose.prod.yml` |

## Modos de Ejecución

### Desarrollo Local

```bash
# Carga automáticamente docker-compose.override.yml
docker compose up -d
```

**Características:**
- Bind mount del código fuente (cambios en tiempo real)
- Puerto 8080 para debug
- MySQL expuesto en puerto 3306
- Vite HMR en puerto 5173
- Migraciones automáticas

### Producción

```bash
# Carga docker-compose.prod.yml encima de docker-compose.yml
docker compose -f docker-compose.yml -f docker-compose.prod.yml up -d
```

**Características:**
- Sin bind mounts de código
- Límites de recursos (CPU/RAM)
- Puerto MySQL NO expuesto al host
- `restart: unless-stopped` en todos los servicios

---

## Servicios Detallados

### 1. App (Laravel + PHP-FPM)

```yaml
app:
  build:
    context: .
    dockerfile: Dockerfile
    target: runtime
  container_name: iot_app
  restart: unless-stopped
```

**Variables de entorno principales:**

| Variable | Descripción | Desarrollo | Producción |
|----------|-------------|------------|------------|
| `APP_ENV` | Entorno de ejecución | `local` | `production` |
| `APP_DEBUG` | Modo debug | `true` | `false` |
| `RUN_MIGRATIONS` | Ejecutar migraciones al iniciar | `true` | `true` |

**Dependencias:** MySQL (healthy), InfluxDB (healthy), Mosquitto (healthy)

**Healthcheck:**
```yaml
healthcheck:
  test: ["CMD", "php-fpm", "-t"]
  interval: 10s
  timeout: 5s
  retries: 5
  start_period: 20s
```

### 2. Webserver (Nginx)

```yaml
webserver:
  image: nginx:alpine
  container_name: iot_webserver
  ports:
    - "${NGINX_PORT:-80}:80"
```

**Volumes montados:**
- `.:/app:ro` — Código fuente (solo lectura)
- `storage_data:/app/storage:ro` — Datos de almacenamiento
- `./docker/nginx/default.conf:/etc/nginx/conf.d/default.conf:ro` — Configuración Nginx

**Healthcheck:**
```yaml
healthcheck:
  test: ["CMD", "wget", "--no-verbose", "--tries=1", "--spider", "http://127.0.0.1/"]
  interval: 30s
  timeout: 10s
  retries: 3
```

### 3. MySQL

```yaml
mysql:
  image: mysql:8.0
  container_name: iot_mysql
  environment:
    - MYSQL_ROOT_PASSWORD=${DB_PASSWORD:-root}
    - MYSQL_DATABASE=${DB_DATABASE:-iot_project}
```

**Configuración del servidor:**
```yaml
command: >
  --default-authentication-plugin=mysql_native_password
  --character-set-server=utf8mb4
  --collation-server=utf8mb4_unicode_ci
```

**Healthcheck:**
```yaml
healthcheck:
  test: ["CMD-SHELL", "mysqladmin ping -h localhost -u root -p\"$$MYSQL_ROOT_PASSWORD\""]
  interval: 10s
  timeout: 5s
  retries: 10
  start_period: 30s
```

### 4. InfluxDB

```yaml
influxdb:
  image: influxdb:2.7
  container_name: iot_influxdb
  environment:
    - DOCKER_INFLUXDB_INIT_MODE=setup
    - DOCKER_INFLUXDB_INIT_USERNAME=${DOCKER_INFLUXDB_INIT_USERNAME:-admin}
    - DOCKER_INFLUXDB_INIT_PASSWORD=${DOCKER_INFLUXDB_INIT_PASSWORD:-changeme}
    - DOCKER_INFLUXDB_INIT_ORG=${DOCKER_INFLUXDB_INIT_ORG:-iot_project}
    - DOCKER_INFLUXDB_INIT_BUCKET=${DOCKER_INFLUXDB_INIT_BUCKET:-biobit}
    - DOCKER_INFLUXDB_INIT_ADMIN_TOKEN=${DOCKER_INFLUXDB_INIT_ADMIN_TOKEN:-changeme_generate_a_real_token}
```

**Inicialización automática:** Configura usuario, organización y bucket al primer arranque.

### 5. Mosquitto (MQTT Broker)

```yaml
mosquitto:
  image: eclipse-mosquitto:2
  container_name: iot_mosquitto
  volumes:
    - mosquitto_data:/mosquitto/data
    - mosquitto_log:/mosquitto/log
    - ./docker/mosquitto/config:/mosquitto/config:ro
```

**Healthcheck:**
```yaml
healthcheck:
  test: ["CMD-SHELL", "mosquitto_sub -t '$$SYS/broker/version' -C 1 -W 1 || exit 1"]
  interval: 10s
  timeout: 5s
  retries: 5
  start_period: 5s
```

### 6. Vite (Desarrollo)

```yaml
vite:
  image: node:22-alpine
  container_name: iot_vite
  ports:
    - "5173:5173"
  command: sh -c "cp /src/package.json /app/package.json && npm install && npm run dev -- --host 0.0.0.0"
```

**Solo se ejecuta en modo desarrollo** (desactivado en producción).

---

## Redes

```yaml
networks:
  iot_network:
    driver: bridge
```

Todos los servicios comparten la red `iot_network`. Comunicación interna por nombre de servicio (ej. `mysql`, `influxdb`, `mosquitto`).

## Volúmenes

| Volumen | Propósito | Persistencia |
|---------|-----------|--------------|
| `mysql_data` | Datos de MySQL | Sí |
| `influxdb_data` | Datos de InfluxDB | Sí |
| `mosquitto_data` | Estado de Mosquitto | Sí |
| `mosquitto_log` | Logs de Mosquitto | Sí |
| `storage_data` | Imágenes de perfil, exports | Sí |
| `node_modules_data` | Dependencias Node.js | Sí |

**Importante:** Los volúmenes persisten datos después de `docker compose down`. Usar `docker compose down -v` para eliminarlos.
