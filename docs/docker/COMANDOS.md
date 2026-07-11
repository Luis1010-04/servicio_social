# Comandos Docker — Referencia Rápida

## Gestión de Servicios

```bash
# Iniciar todos los servicios (desarrollo)
docker compose up -d

# Iniciar todos los servicios (producción)
docker compose -f docker-compose.yml -f docker-compose.prod.yml up -d

# Detener todos los servicios
docker compose down

# Detener y eliminar volúmenes (⚠️ ELIMINA DATOS)
docker compose down -v

# Reconstruir imágenes y levantar
docker compose up -d --build

# Ver estado de servicios
docker compose ps

# Ver logs en tiempo real (todos los servicios)
docker compose logs -f

# Ver logs de un servicio específico
docker compose logs -f app
docker compose logs -f mysql
docker compose logs -f webserver
docker compose logs -f influxdb
docker compose logs -f mosquitto

# Reiniciar un servicio específico
docker compose restart app
```

## Laravel via Docker

```bash
# Ejecutar Artisan
docker compose exec app php artisan

# Ejecutar migraciones
docker compose exec app php artisan migrate

# Ejecutar migraciones con seeders
docker compose exec app php artisan migrate --seed

# Generar clave de aplicación
docker compose run --rm app php artisan key:generate

# Limpiar caché
docker compose exec app php artisan cache:clear
docker compose exec app php artisan config:clear
docker compose exec app php artisan route:clear
docker compose exec app php artisan view:clear

# Limpiar todo
docker compose exec app php artisan optimize:clear

# Ejecutar tareas de mantenimiento
docker compose exec app php artisan schedule:run

# Verificar rutas
docker compose exec app php artisan route:list
```

## Base de Datos

```bash
# Acceder a MySQL (CLI)
docker compose exec mysql mysql -u root -p

# Backup de MySQL
docker compose exec -T mysql mysqldump -u root -proot iot_project > backup.sql

# Restaurar MySQL
docker compose exec -T mysql mysql -u root -proot iot_project < backup.sql

# Acceder a InfluxDB (CLI)
docker compose exec influxdb influx

# Verificar salud de InfluxDB
curl http://localhost:8086/health
```

## MQTT

```bash
# Probar broker MQTT (suscribirse)
mosquitto_sub -h localhost -t "test/#" -v

# Publicar mensaje de prueba
mosquitto_pub -h localhost -t "test" -m "Hello IoT"

# Suscribirse a todos los tópicos del sistema
mosquitto_sub -h localhost -t "v1/#" -v
```

## Construcción de Imágenes

```bash
# Construir imagen de la app
docker build -t iot-project-app .

# Construir sin caché
docker build --no-cache -t iot-project-app .

# Verificar imágenes locales
docker images | grep iot

# Eliminar imagen
docker rmi iot-project-app
```

## Utilidades

```bash
# Verificar consumo de recursos
docker stats

# Verificar volúmenes
docker volume ls

# Limpiar imágenes no utilizadas
docker system prune

# Verificar red
docker network inspect iot_network_social
```

## Comandos de Desarrollo

```bash
# Instalar dependencias npm en el contenedor
docker compose exec app npm install

# Compilar assets
docker compose exec app npm run build

# Hot-reload (ya se ejecuta en servicio vite)
# Acceder a http://localhost:5173

# Ejecutar tests
docker compose exec app php artisan test

# Verificar código con Pint
docker compose exec app ./vendor/bin/pint
```

## Solución de Problemas

```bash
# Ver logs de error de un contenedor
docker compose logs --tail=100 app

# Acceder a un contenedor (shell)
docker compose exec app /bin/bash

# Verificar conexiones de red
docker compose exec app ping mysql
docker compose exec app ping influxdb
docker compose exec app ping mosquitto

# Verificar puertos
docker compose exec app nc -zv mysql 3306
docker compose exec app nc -zv influxdb 8086
docker compose exec app nc -zv mosquitto 1883
```
