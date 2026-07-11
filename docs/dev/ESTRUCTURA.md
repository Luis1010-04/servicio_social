# Estructura del Proyecto — IoT_Project

## Tabla de Contenidos

1. [Árbol de Directorios](#1-árbol-de-directorios)
2. [Descripción de Carpetas](#2-descripción-de-carpetas)
3. [Archivos Principales](#3-archivos-principales)

---

## 1. Árbol de Directorios

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
├── bootstrap/
│   └── cache/                     # Caché de Laravel
├── config/
│   ├── app.php
│   ├── database.php
│   └── services.php               # Configuración de servicios IoT
├── database/
│   ├── migrations/                # Migraciones de la base de datos
│   └── seeders/                   # Datos iniciales del sistema
├── docker/
│   ├── entrypoint.sh              # Script de inicio del contenedor
│   ├── mosquitto/
│   │   └── config/
│   │       └── mosquitto.conf     # Configuración de Mosquitto
│   └── nginx/
│       └── default.conf           # Configuración de Nginx
├── docs/
│   ├── docker/                    # Documentación Docker
│   └── dev/                       # Documentación de desarrollo
├── public/                        # Assets públicos
│   ├── build/                     # Assets compilados (Vite)
│   ├── css/
│   ├── js/
│   └── images/
├── resources/
│   └── views/                     # Vistas Blade (52 archivos)
│       ├── layouts/
│       ├── shared/
│       └── modules/
├── routes/
│   └── web.php                    # Definición de rutas
├── storage/
│   ├── app/
│   │   └── public/                # Imágenes de perfil, exports
│   ├── framework/
│   │   ├── cache/
│   │   ├── sessions/
│   │   └── views/
│   └── logs/
├── tests/                         # Tests PHPUnit
├── .dockerignore                  # Archivos excluidos de Docker
├── .editorconfig                  # Configuración del editor
├── .env                           # Variables de entorno (no versionado)
├── .env.example                   # Plantilla de variables de entorno
├── .gitattributes
├── .gitignore
├── artisan                        # CLI de Laravel
├── composer.json                  # Dependencias PHP
├── composer.lock
├── DEPLOY.md                      # Guía de despliegue Docker
├── docker-compose.yml             # Compose base
├── docker-compose.override.yml    # Compose desarrollo
├── docker-compose.prod.yml        # Compose producción
├── Dockerfile                     # Imagen multi-stage
├── LICENSE
├── package.json                   # Dependencias JavaScript
├── package-lock.json
├── phpunit.xml                    # Configuración de tests
├── README.md                      # Documentación principal
├── tailwind.config.js
├── vite.config.js                 # Configuración de Vite
└── vendor/                        # Dependencias Composer
```

---

## 2. Descripción de Carpetas

### `app/`

Contiene la lógica de negocio de la aplicación Laravel.

| Subcarpeta | Propósito |
|-----------|-----------|
| `Http/Controllers/` | Manejo de peticiones HTTP |
| `Http/Middleware/` | Middleware de autenticación y autorización |
| `Models/` | Modelos Eloquent (acceso a datos) |
| `Jobs/` | Tareas en cola (background processing) |

### `config/`

Archivos de configuración de Laravel.

| Archivo | Propósito |
|---------|-----------|
| `app.php` | Configuración general de la aplicación |
| `database.php` | Configuración de bases de datos |
| `services.php` | Configuración de servicios externos (MQTT, InfluxDB) |

### `database/`

| Subcarpeta | Propósito |
|-----------|-----------|
| `migrations/` | Definición de esquema de la base de datos |
| `seeders/` | Datos iniciales (usuarios, catálogos, etc.) |

### `docker/`

Configuración de Docker y servicios.

| Archivo | Propósito |
|---------|-----------|
| `entrypoint.sh` | Script de inicio del contenedor PHP |
| `mosquitto/config/mosquitto.conf` | Configuración del broker MQTT |
| `nginx/default.conf` | Configuración del servidor web Nginx |

### `docs/`

Documentación del proyecto.

| Subcarpeta | Contenido |
|-----------|-----------|
| `docker/` | Documentación de Docker y despliegue |
| `dev/` | Documentación de desarrollo y arquitectura |

### `public/`

Directorio accesible públicamente desde el servidor web.

| Subcarpeta | Propósito |
|-----------|-----------|
| `build/` | Assets compilados por Vite (CSS/JS minificados) |
| `images/` | Imágenes estáticas del sitio |

### `resources/`

Recursos de la aplicación (vistas, assets sin compilar).

| Subcarpeta | Propósito |
|-----------|-----------|
| `views/` | Plantillas Blade (52 archivos) |
| `views/layouts/` | Plantillas base (main, auth) |
| `views/shared/` | Componentes compartidos |
| `views/modules/` | Módulos por funcionalidad |

### `routes/`

Definición de rutas de la aplicación.

| Archivo | Propósito |
|---------|-----------|
| `web.php` | Todas las rutas HTTP del sistema |

### `storage/`

Almacenamiento de archivos generados.

| Subcarpeta | Propósito |
|-----------|-----------|
| `app/public/` | Imágenes de perfil, exports PDF/Excel |
| `framework/cache/` | Caché compilado |
| `framework/sessions/` | Sesiones de usuarios |
| `framework/views/` | Vistas Blade compiladas |
| `logs/` | Logs de Laravel |

### `tests/`

Tests automatizados de la aplicación.

---

## 3. Archivos Principales

### Archivos de Configuración

| Archivo | Propósito |
|---------|-----------|
| `composer.json` | Dependencias PHP y scripts de Composer |
| `package.json` | Dependencias JavaScript y scripts npm |
| `vite.config.js` | Configuración del empaquetador Vite |
| `tailwind.config.js` | Configuración de Tailwind CSS |
| `phpunit.xml` | Configuración de tests PHPUnit |

### Archivos Docker

| Archivo | Propósito |
|---------|-----------|
| `Dockerfile` | Definición de la imagen multi-stage |
| `docker-compose.yml` | Servicios base (app, nginx, mysql, influxdb, mosquitto) |
| `docker-compose.override.yml` | Configuración de desarrollo local |
| `docker-compose.prod.yml` | Configuración de producción |
| `.dockerignore` | Archivos excluidos del build de Docker |

### Archivos de Documentación

| Archivo | Propósito |
|---------|-----------|
| `README.md` | Documentación principal del proyecto |
| `DEPLOY.md` | Guía de despliegue con Docker |
| `LICENSE` | Licencia MIT del proyecto |

### Archivos de Entorno

| Archivo | Propósito |
|---------|-----------|
| `.env.example` | Plantilla de variables de entorno |
| `.env` | Variables de entorno reales (no versionado) |
| `.gitignore` | Archivos ignorados por Git |
| `.editorconfig` | Configuración del editor de código |
