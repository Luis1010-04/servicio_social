# IoT_Project — Plataforma de Monitoreo y Control de Variables Ambientales

[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=flat-square&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=flat-square&logo=php&logoColor=white)](https://www.php.net)
[![InfluxDB](https://img.shields.io/badge/InfluxDB-2.x-22ADF6?style=flat-square&logo=influxdb&logoColor=white)](https://www.influxdata.com/)
[![MQTT](https://img.shields.io/badge/MQTT-Eclipse%20Mosquitto-660066?style=flat-square&logo=eclipsemosquitto&logoColor=white)](https://mosquitto.org/)
[![License](https://img.shields.io/badge/License-MIT-blue.svg?style=flat-square)](LICENSE)

Plataforma web híbrida para la administración, centralización y análisis de telemetría proveniente de nodos sensores IoT. El sistema organiza los dispositivos en una topología jerárquica de hardware (Maestros y Esclavos) comunicados mediante el protocolo MQTT.

---

## Características Principales

- **Persistencia Dual** — MySQL para datos relacionales (usuarios, roles, hardware) + InfluxDB para series temporales de alta frecuencia (telemetría de sensores).
- **Control Bidireccional** — Lectura de sensores en tiempo real y envío de comandos MQTT a actuadores (relevadores, servos, bombas).
- **Seguridad por Capas** — Autenticación nativa de Laravel, middleware de roles (`Admin` / `Usuario`) y segregación de datos por usuario.
- **Arquitectura IoT Perimetral** — Nodos Esclavos capturan variables, Nodos Maestros concentran datos y el Broker MQTT (Eclipse Mosquitto) orquesta la mensajería.

---

## Stack Tecnológico

| Capa | Tecnología |
|------|-----------|
| **Backend** | PHP 8.2 / Laravel 12.x |
| **Frontend** | Blade + Tailwind CSS v4 + Bootstrap 5.3 + Chart.js + DataTables |
| **Base de Datos Relacional** | MySQL / MariaDB 10.4 (InnoDB) |
| **Base de Datos Temporal** | InfluxDB v2.7+ |
| **Mensajería IoT** | Eclipse Mosquitto v2.x (MQTT) via Docker |
| **Servidor Web** | Apache 2.4 (XAMPP/LAMPP) |
| **Empaquetador** | Vite |
| **Gestor de Dependencias** | Composer |

---

## Requisitos Previos

| Requisito | Versión Mínima | Propósito |
|-----------|---------------|-----------|
| PHP | 8.2 | Motor de ejecución del backend |
| Composer | 2.x | Gestión de dependencias PHP |
| MySQL/MariaDB | 10.4 | Almacenamiento relacional |
| Node.js | 18+ | Compilación de assets (Vite) |
| Docker | 24+ | Contenedor de Eclipse Mosquitto |
| Git | 2.x | Control de versiones |

---

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

Para instrucciones detalladas, consulta [docs/INSTALACION.md](docs/INSTALACION.md).

---

## Estructura del Proyecto

```
servicio_social/
├── app/
│   ├── Http/
│   │   ├── Controllers/          # Controladores (24 clases)
│   │   │   ├── AuthController.php
│   │   │   ├── DashboardController.php
│   │   │   ├── ComponenteController.php
│   │   │   ├── admin/             # Controladores administrativos
│   │   │   ├── user/              # Controladores de usuario final
│   │   │   └── shared/            # Controladores compartidos
│   │   └── Middleware/
│   │       └── Checkrol.php       # Middleware de autorización por rol
│   ├── Models/                    # Modelos Eloquent (12 tablas)
│   └── Jobs/                      # Tareas en cola
├── config/
│   ├── app.php
│   ├── database.php
│   └── services.php               # Configuración de servicios IoT
├── database/
│   ├── migrations/                # Migraciones de la base de datos
│   └── seeders/                   # Datos iniciales del sistema
├── docs/                          # Documentación técnica
├── public/                        # Assets públicos
├── resources/
│   └── views/                     # Vistas Blade (52 archivos)
│       ├── layouts/
│       ├── shared/
│       └── modules/
├── routes/
│   └── web.php                    # Definición de rutas
├── .env.example                   # Plantilla de variables de entorno
├── composer.json
├── package.json
└── vite.config.js
```

---

## Documentación

| Documento | Descripción |
|-----------|------------|
| [Instalación](docs/INSTALACION.md) | Guía completa de instalación y configuración del entorno |
| [Arquitectura](docs/ARQUITECTURA.md) | Diseño del sistema, modelos de datos y patrones de diseño |
| [API y Rutas](docs/API.md) | Endpoints HTTP, métodos HTTP y permisos |
| [Manual de Usuario](docs/MANUAL_USUARIO.md) | Guía operativa para usuarios finales y administradores |
| [Contribución](docs/CONTRIBUCION.md) | Guía para contribuidores al proyecto |

---

## Modelo de Datos (Resumen)

```
users ──1:N── ubicaciones ──1:N── maestros_usuarios ──1:N── maestros_esclavos
                                          │                        │
                                          │                        └── N:1 ── esclavos_catalogo
                                          │
                                    maestros_catalogo
                                    
componentes ──N:1── unidades_de_medida
     │
     └── N:M ── esclavos_catalogo (via detalle_esclavo_componentes)
```

---

## Credenciales por Defecto (Desarrollo)

> **IMPORTANTE:** Estas credenciales son exclusivas para entorno de desarrollo. Cambiarlas antes de desplegar en producción.

| Campo | Valor |
|-------|-------|
| Email | `admin@admin.com` |
| Contraseña | `admin` |
| Rol | `Admin` |

---

## Rendimiento y Escalabilidad

- **InfluxDB** maneja la ingestión masiva de telemetría sin impactar el rendimiento de Apache.
- **MySQL** se mantiene exclusivamente para operaciones transaccionales (CRUD de usuarios, hardware, configuración).
- **MQTT** permite comunicación asíncrona de baja latencia entre la plataforma web y los dispositivos IoT.
- Las consultas a InfluxDB utilizan el lenguaje **Flux** para análisis temporal eficiente.

---

## Licencia

Este proyecto está bajo la licencia MIT. Ver el archivo [LICENSE](LICENSE) para más detalles.

---

## Autor

**Luis** — [GitHub: Luis1010-04](https://github.com/Luis1010-04)
