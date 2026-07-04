# Guía de Instalación — IoT_Project

## Tabla de Contenidos

1. [Requisitos del Sistema](#1-requisitos-del-sistema)
2. [Preparación del Entorno](#2-preparación-del-entorno)
3. [Instalación del Proyecto](#3-instalación-del-proyecto)
4. [Configuración de Bases de Datos](#4-configuración-de-bases-de-datos)
5. [Configuración de Variables de Entorno](#5-configuración-de-variables-de-entorno)
6. [Despliegue del Broker MQTT](#6-despliegue-del-broker-mqtt)
7. [Verificación del Sistema](#7-verificación-del-sistema)
8. [Solución de Problemas Comunes](#8-solución-de-problemas-comunes)

---

## 1. Requisitos del Sistema

### Hardware Mínimo

| Componente | Especificación |
|-----------|---------------|
| Procesador | 2 cores / 1.5 GHz |
| RAM | 4 GB |
| Disco | 20 GB libres |
| Red | Conexión a internet (para dependencias) |

### Software Requerido

| Software | Versión | URL de Descarga |
|----------|---------|-----------------|
| PHP | >= 8.2 | [php.net](https://www.php.net/) |
| Composer | >= 2.0 | [getcomposer.org](https://getcomposer.org/) |
| MySQL/MariaDB | >= 10.4 | [mariadb.org](https://mariadb.org/) |
| Node.js | >= 18 | [nodejs.org](https://nodejs.org/) |
| Docker | >= 24 | [docs.docker.com](https://docs.docker.com/engine/install/) |
| Git | >= 2.0 | [git-scm.com](https://git-scm.com/) |

### Extensiones PHP Requeridas

Las siguientes extensiones deben estar habilitadas en `php.ini`:

```ini
extension=pdo_mysql
extension=openssl
extension=curl
extension=mbstring
extension=xml
extension=bcmath
extension=fileinfo
extension=tokenizer
extension=json
```

---

## 2. Preparación del Entorno

### Opción A: Windows (XAMPP)

1. Descargar e instalar [XAMPP](https://www.apachefriends.org/) con PHP 8.2+.
2. Iniciar los servicios **Apache** y **MySQL** desde el panel de control.
3. Instalar [Docker Desktop](https://www.docker.com/products/docker-desktop/) y reiniciar.
4. Instalar [Git for Windows](https://git-scm.com/download/win).

### Opción B: Linux (LAMPP)

```bash
# Instalar dependencias del sistema
sudo apt update && sudo apt install -y git curl unzip

# Instalar Docker
sudo apt install -y docker.io docker-compose
sudo systemctl enable docker && sudo systemctl start docker
sudo usermod -aG docker $USER
# Cerrar y abrir sesión para aplicar permisos de docker
```

---

## 3. Instalación del Proyecto

### 3.1 Clonar el Repositorio

```bash
# Opción A: Desde GitHub
git clone https://github.com/Luis1010-04/servicio_social.git
cd servicio_social

# Opción B: En LAMPP (Linux)
cd /opt/lampp/htdocs
sudo git clone https://github.com/Luis1010-04/servicio_social.git
cd servicio_social
sudo chown -R $USER:$USER .
```

### 3.2 Instalar Dependencias PHP

```bash
composer install
```

### 3.3 Instalar Dependencias JavaScript

```bash
npm install
```

### 3.4 Compilar Assets

```bash
# Para desarrollo (con hot-reload)
npm run dev

# Para producción (minificado)
npm run build
```

---

## 4. Configuración de Bases de Datos

### 4.1 MySQL / MariaDB

Crear la base de datos vacía:

```sql
CREATE DATABASE iot_project CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

O mediante la línea de comandos:

```bash
mysql -u root -e "CREATE DATABASE iot_project CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

### 4.2 InfluxDB

1. Acceder a la interfaz de InfluxDB en `http://localhost:8086`.
2. Crear un **Token de API** desde Settings > API Tokens.
3. Crear un **Bucket** llamado `biobit` (o el nombre que prefieras).
4. Crear una **Organización** si no existe.

Registrar los valores generados para configurarlos en `.env`:

| Campo | Descripción |
|-------|------------|
| `INFLUXDB_URL` | URL del servidor InfluxDB (ej. `http://localhost:8086`) |
| `INFLUXDB_TOKEN` | Token de autenticación generado |
| `INFLUXDB_BUCKET` | Nombre del bucket para telemetría |
| `INFLUXDB_ORG` | Nombre de la organización |

---

## 5. Configuración de Variables de Entorno

### 5.1 Crear Archivo `.env`

```bash
cp .env.example .env
```

### 5.2 Generar Clave de Aplicación

```bash
php artisan key:generate
```

### 5.3 Editar `.env`

Configurar las siguientes secciones según tu entorno:

```env
# ============================================
# APLICACIÓN
# ============================================
APP_NAME=IoT_Project
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost/servicio_social/public

# ============================================
# BASE DE DATOS RELACIONAL (MySQL)
# ============================================
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=iot_project
DB_USERNAME=root
DB_PASSWORD=

# ============================================
# BASE DE DATOS TEMPORAL (InfluxDB)
# ============================================
INFLUXDB_URL=http://127.0.0.1:8086
INFLUXDB_TOKEN=tu_token_aqui
INFLUXDB_BUCKET=biobit
INFLUXDB_ORG=tu_organizacion

# ============================================
# MQTT (Eclipse Mosquitto)
# ============================================
MQTT_HOST=127.0.0.1
MQTT_PORT=1883
```

---

## 6. Despliegue del Broker MQTT

### 6.1 Con Docker (Recomendado)

```bash
# Ejecutar Eclipse Mosquitto
docker run -d \
  --name mosquitto \
  -p 1883:1883 \
  -v $(pwd)/docker/mosquitto/config:/mosquitto/config \
  eclipse-mosquitto
```

### 6.2 Verificar el Contenedor

```bash
docker ps | grep mosquitto
# Debería mostrar el contenedor "mosquitto" en estado "Up"
```

### 6.3 Configuración Personalizada (Opcional)

Crear archivo `docker/mosquitto/config/mosquitto.conf`:

```
listener 1883
allow_anonymous true
persistence true
persistence_location /mosquitto/data/
log_dest file /mosquitto/log/mosquitto.log
```

---

## 7. Verificación del Sistema

### 7.1 Ejecutar Migraciones

```bash
php artisan migrate --seed
```

### 7.2 Limpiar Caché

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

### 7.3 Iniciar el Servidor

```bash
php artisan serve
```

### 7.4 Acceder a la Plataforma

Abrir el navegador en:

```
http://localhost:8000
```

Credenciales por defecto:

| Campo | Valor |
|-------|-------|
| Email | `admin@admin.com` |
| Contraseña | `admin` |

---

## 8. Solución de Problemas Comunes

### Error: "HTTP 500 Internal Server Error"

```bash
# Verificar permisos en Linux
sudo chown -R $USER:daemon storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# Verificar que .env existe
cp .env.example .env
php artisan key:generate
```

### Error: "SQLSTATE[HY000] Connection refused"

```bash
# Verificar que MySQL está corriendo
# En XAMPP: revisar panel de control
# En Linux:
sudo systemctl status mysql
```

### Error: "Class 'App\Http\Controllers\X' not found"

```bash
php artisan config:clear
php artisan route:clear
composer dump-autoload
```

### Error: MQTT no conecta

```bash
# Verificar que Docker está corriendo
docker ps

# Verificar que el puerto 1883 está libre
netstat -an | grep 1883

# Reiniciar el contenedor
docker restart mosquitto
```

### Error: InfluxDB no responde

```bash
# Verificar el contenedor de InfluxDB
docker ps | grep influx

# Verificar conectividad
curl http://localhost:8086/health
```

---

## Comandos de Desarrollo Útiles

```bash
# Ejecutar tests
php artisan test

# Verificar código con PHP-CS-Fixer
./vendor/bin/pint

# Verificar tipos (si se configura PHPStan)
./vendor/bin/phpstan analyse

# Monitorear logs en tiempo real
php artisan pail

# Limpiar todos los caches
php artisan optimize:clear
```
