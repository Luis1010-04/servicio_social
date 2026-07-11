# Variables de Entorno — Docker

## Archivo `.env`

Todas las configuraciones de Docker se controlan mediante el archivo `.env` en la raíz del proyecto. Nunca versionar valores reales.

---

## Puertos Expuestos al Host

| Variable | Puerto por Defecto | Descripción |
|----------|-------------------|-------------|
| `NGINX_PORT` | `80` | Puerto del servidor web (HTTP) |
| `MQTT_PORT` | `1883` | Puerto del broker MQTT |
| `INFLUXDB_PORT` | `8086` | Puerto de la API de InfluxDB |
| `MYSQL_PORT` | `3306` | Puerto de MySQL (solo desarrollo) |

```env
# Puertos expuestos al host
NGINX_PORT=80
MQTT_PORT=1883
INFLUXDB_PORT=8086
# MYSQL_PORT=3306  # Solo para debugging en desarrollo
```

---

## Configuración de Laravel

| Variable | Descripción | Valores |
|----------|-------------|---------|
| `APP_NAME` | Nombre de la aplicación | `Laravel` |
| `APP_ENV` | Entorno de ejecución | `local`, `production` |
| `APP_DEBUG` | Modo debug | `true`, `false` |
| `APP_URL` | URL base | `http://localhost` |
| `APP_KEY` | Clave de cifrado (generada) | Base64 string |

```env
APP_NAME=Laravel
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost
```

---

## Base de Datos Relacional (MySQL)

| Variable | Descripción | Valor por Defecto |
|----------|-------------|-------------------|
| `DB_CONNECTION` | Tipo de conexión | `mysql` |
| `DB_HOST` | Host de MySQL | `mysql` (nombre de servicio Docker) |
| `DB_PORT` | Puerto de MySQL | `3306` |
| `DB_DATABASE` | Nombre de la BD | `iot_project` |
| `DB_USERNAME` | Usuario de MySQL | `root` |
| `DB_PASSWORD` | Contraseña de MySQL | `root` |

```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=iot_project
DB_USERNAME=root
DB_PASSWORD=root
```

**Nota:** En Docker, `DB_HOST` debe ser `mysql` (nombre del servicio), no `127.0.0.1`.

---

## InfluxDB (Series Temporales)

### Configuración de Laravel

| Variable | Descripción | Valor por Defecto |
|----------|-------------|-------------------|
| `INFLUXDB_URL` | URL del servidor InfluxDB | `http://influxdb:8086` |
| `INFLUXDB_TOKEN` | Token de autenticación | Token generado |
| `INFLUXDB_BUCKET` | Nombre del bucket | `biobit` |
| `INFLUXDB_ORG` | Nombre de la organización | `iot_project` |

```env
INFLUXDB_URL=http://influxdb:8086
INFLUXDB_TOKEN=your_influxdb_token
INFLUXDB_BUCKET=biobit
INFLUXDB_ORG=iot_project
```

### Configuración del Contenedor (Init)

| Variable | Descripción | Valor por Defecto |
|----------|-------------|-------------------|
| `DOCKER_INFLUXDB_INIT_MODE` | Modo de inicialización | `setup` |
| `DOCKER_INFLUXDB_INIT_USERNAME` | Usuario admin | `admin` |
| `DOCKER_INFLUXDB_INIT_PASSWORD` | Contraseña admin | `changeme` |
| `DOCKER_INFLUXDB_INIT_ORG` | Organización | `iot_project` |
| `DOCKER_INFLUXDB_INIT_BUCKET` | Bucket | `biobit` |
| `DOCKER_INFLUXDB_INIT_ADMIN_TOKEN` | Token admin | Token seguro |

```env
DOCKER_INFLUXDB_INIT_USERNAME=admin
DOCKER_INFLUXDB_INIT_PASSWORD=changeme
DOCKER_INFLUXDB_INIT_ORG=iot_project
DOCKER_INFLUXDB_INIT_BUCKET=biobit
DOCKER_INFLUXDB_INIT_ADMIN_TOKEN=changeme_generate_a_real_token_with_openssl
```

---

## MQTT (Eclipse Mosquitto)

| Variable | Descripción | Valor por Defecto |
|----------|-------------|-------------------|
| `MQTT_HOST` | Host del broker MQTT | `mosquitto` (nombre de servicio) |
| `MQTT_PORT` | Puerto del broker | `1883` |

```env
MQTT_HOST=mosquitto
MQTT_PORT=1883
```

---

## Control de Migraciones

| Variable | Descripción | Valores |
|----------|-------------|---------|
| `RUN_MIGRATIONS` | Ejecutar migraciones al iniciar | `true`, `false` |

```env
RUN_MIGRATIONS=true
```

---

## Configuración de Sesiones

| Variable | Descripción | Valor por Defecto |
|----------|-------------|-------------------|
| `SESSION_DRIVER` | Motor de sesiones | `database` |
| `SESSION_LIFETIME` | Duración en minutos | `120` |

```env
SESSION_DRIVER=database
SESSION_LIFETIME=120
```

---

## Variables por Entorno

### Producción

```env
APP_ENV=production
APP_DEBUG=false
RUN_MIGRATIONS=true
NGINX_PORT=80
MQTT_PORT=1883
INFLUXDB_PORT=8086
```

### Desarrollo

```env
APP_ENV=local
APP_DEBUG=true
RUN_MIGRATIONS=true
NGINX_PORT=80
MYSQL_PORT=3306
```

---

## Generar Token Seguro para InfluxDB

```bash
# Usando OpenSSL
openssl rand -hex 32

# Usando Laravel
php artisan key:generate --show
```

Copiar el resultado a `DOCKER_INFLUXDB_INIT_ADMIN_TOKEN` en `.env`.
