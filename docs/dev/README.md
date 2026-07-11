# Documentación de Desarrollo — IoT_Project

## Visión General

IoT_Project es una plataforma web híbrida para la administración, centralización y análisis de telemetría proveniente de nodos sensores IoT. El sistema organiza los dispositivos en una topología jerárquica de hardware (Maestros y Esclavos) comunicados mediante el protocolo MQTT.

## Stack Tecnológico

| Capa | Tecnología |
|------|-----------|
| **Backend** | PHP 8.2 / Laravel 12.x |
| **Frontend** | Blade + Tailwind CSS v4 + Bootstrap 5.3 + Chart.js + DataTables |
| **Base de Datos Relacional** | MySQL / MariaDB 10.4 (InnoDB) |
| **Base de Datos Temporal** | InfluxDB v2.7+ |
| **Mensajería IoT** | Eclipse Mosquitto v2.x (MQTT) |
| **Servidor Web** | Apache 2.4 (XAMPP/LAMPP) o Nginx (Docker) |
| **Empaquetador** | Vite |
| **Gestor de Dependencias** | Composer |

## Documentación

| Documento | Descripción |
|-----------|-------------|
| [INSTALACION.md](INSTALACION.md) | Guía completa de instalación y configuración del entorno |
| [ARQUITECTURA.md](ARQUITECTURA.md) | Diseño del sistema, modelos de datos y patrones de diseño |
| [API.md](API.md) | Endpoints HTTP, métodos HTTP y permisos |
| [ESTRUCTURA.md](ESTRUCTURA.md) | Estructura de directorios del proyecto |
| [CONTRIBUCION.md](CONTRIBUCION.md) | Guía para contribuidores al proyecto |

## Requisitos del Sistema

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

## Instalación Rápida

```bash
# 1. Clonar el repositorio
git clone https://github.com/Luis1010-04/servicio_social.git
cd servicio_social

# 2. Instalar dependencias PHP
composer install

# 3. Configurar entorno
cp .env.example .env
php artisan key:generate

# 4. Editar .env con tus credenciales de MySQL e InfluxDB

# 5. Crear la base de datos y ejecutar migraciones
php artisan migrate --seed

# 6. Instalar dependencias JavaScript y compilar assets
npm install
npm run build

# 7. Levantar el Broker MQTT (Docker)
docker run -d --name mosquitto -p 1883:1883 eclipse-mosquitto

# 8. Iniciar el servidor
php artisan serve
```

## Credenciales por Defecto (Desarrollo)

| Campo | Valor |
|-------|-------|
| Email | `admin@admin.com` |
| Contraseña | `admin` |
| Rol | `Admin` |

> **IMPORTANTE:** Estas credenciales son exclusivas para entorno de desarrollo. Cambiarlas antes de desplegar en producción.
