# Documentación Docker — IoT_Project

## Visión General

IoT_Project está contenerizado usando Docker y Docker Compose. El stack completo incluye 5 servicios interconectados mediante una red bridge dedicada.

## Stack de Servicios

| Servicio | Imagen | Puerto | Propósito |
|----------|--------|--------|-----------|
| **app** | Build local (Dockerfile) | 9000 (interno) | Laravel 12.x + PHP 8.2-FPM |
| **webserver** | nginx:alpine | 80 | Servidor web inverso |
| **mysql** | mysql:8.0 | 3306 (solo desarrollo) | Base de datos relacional |
| **influxdb** | influxdb:2.7 | 8086 | Series temporales (telemetría IoT) |
| **mosquitto** | eclipse-mosquitto:2 | 1883 | Broker MQTT |
| **vite** | node:22-alpine | 5173 | Hot-reload en desarrollo |

## Arquitectura Docker

```
                    ┌──────────────────────────────────────┐
                    │            iot_network                │
                    │            (bridge)                   │
                    │                                       │
    Puerto 80  ────┤  ┌─────────────┐                      │
                    │  │  webserver  │ (nginx:alpine)        │
                    │  └──────┬──────┘                      │
                    │         │ fastcgi :9000               │
                    │  ┌──────▼──────┐                      │
                    │  │     app     │ (PHP 8.2-FPM)        │
                    │  └──┬────┬──┬──┘                      │
                    │     │    │  │                         │
    Puerto 3306 ───┤  ┌──▼┐   │  └──┐   ┌───────────┐     │
                    │  │ DB│   │     │   │  mosquitto │     │
                    │  └───┘   │     │   └───────────┘     │
    Puerto 8086 ───┤  ┌───────▼┐   └──►┌───────────┐      │
                    │  │influxdb│       │  (MQTT)    │      │
                    │  └────────┘       └───────────┘      │
    Puerto 1883 ───┤                                       │
                    └──────────────────────────────────────┘
```

## Documentación

| Documento | Descripción |
|-----------|-------------|
| [DOCKERFILE.md](DOCKERFILE.md) | Análisis del Dockerfile multi-stage |
| [DOCKER_COMPOSE.md](DOCKER_COMPOSE.md) | Servicios, redes, volúmenes y configuración |
| [COMANDOS.md](COMANDOS.md) | Comandos útiles de gestión diaria |
| [VARIABLES_ENTORNO.md](VARIABLES_ENTORNO.md) | Variables de entorno para Docker |
| [TROUBLESHOOTING.md](TROUBLESHOOTING.md) | Solución de problemas comunes |

## Inicio Rápido

```bash
# 1. Clonar el repositorio
git clone https://github.com/Luis1010-04/servicio_social.git
cd servicio_social

# 2. Configurar entorno
cp .env.example .env
docker compose run --rm app php artisan key:generate

# 3. Levantar en producción
docker compose -f docker-compose.yml -f docker-compose.prod.yml up -d

# 4. Verificar
docker compose ps
curl http://localhost
```

## Imagen en DockerHub

La imagen pre-compilada está disponible en:

```
luis1010/servicio_social:latest
```

```bash
# Descargar y ejecutar directamente
docker pull luis1010/servicio_social:latest
```
