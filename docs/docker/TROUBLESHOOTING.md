# Solución de Problemas — Docker

## Problemas Comunes

### 1. El contenedor `app` no inicia

**Síntomas:**
```
Error: mysqladmin: unable to connect to MySQL server
```

**Causa:** MySQL no está listo cuando `app` intenta conectarse.

**Solución:**
```bash
# Verificar estado de MySQL
docker compose ps mysql

# Ver logs de MySQL
docker compose logs mysql

# Esperar a que MySQL esté healthy y reiniciar app
docker compose restart app
```

---

### 2. Error de conexión a MySQL

**Síntomas:**
```
SQLSTATE[HY000] [2002] Connection refused
```

**Causa:** `DB_HOST` apunta a `127.0.0.1` en lugar del nombre de servicio Docker.

**Solución:**
```bash
# Verificar que DB_HOST=mysql en .env
grep DB_ .env

# Correcto: DB_HOST=mysql
# Incorrecto: DB_HOST=127.0.0.1
```

---

### 3. InfluxDB no responde

**Síntomas:**
```
cURL error 7: Failed to connect to influxdb port 8086: Connection refused
```

**Solución:**
```bash
# Verificar salud de InfluxDB
curl http://localhost:8086/health

# Ver logs
docker compose logs influxdb

# Reiniciar
docker compose restart influxdb
```

---

### 4. MQTT no conecta

**Síntomas:**
```
Connection refused to MQTT broker
```

**Solución:**
```bash
# Verificar que Mosquitto está corriendo
docker compose ps mosquitto

# Probar conexión
mosquitto_sub -h localhost -t "$$SYS/#" -C 1 -W 3

# Ver logs
docker compose logs mosquitto

# Reiniciar
docker compose restart mosquitto
```

---

### 5. Error de permisos en storage/

**Síntomas:**
```
The stream or file "/app/storage/logs/laravel.log" could not be opened: failed to open stream: Permission denied
```

**Solución:**
```bash
# Verificar permisos
docker compose exec app ls -la /app/storage

# Corregir permisos
docker compose exec app chown -R www-data:www-data /app/storage
docker compose exec app chmod -R 775 /app/storage
```

---

### 6. Puerto 80 ya está en uso

**Síntomas:**
```
Error starting userland proxy: Bind for 0.0.0.0:80: address already in use
```

**Solución:**
```bash
# En Windows: detener Apache/IIS
net stop "World Wide Web Publishing Service"

# En Linux: encontrar el proceso
sudo lsof -i :80
sudo kill -9 <PID>

# O cambiar el puerto en .env
NGINX_PORT=8080
```

---

### 7. Nginx muestra 502 Bad Gateway

**Síntomas:** La página carga pero muestra error 502.

**Causa:** PHP-FPM no está corriendo o no responde.

**Solución:**
```bash
# Verificar logs de Nginx
docker compose logs webserver

# Verificar logs de PHP-FPM
docker compose logs app

# Reiniciar ambos servicios
docker compose restart webserver app
```

---

### 8. Las migraciones fallan

**Síntomas:**
```
SQLSTATE[42S01]: Base table or view already exists
```

**Solución:**
```bash
# Verificar estado de migraciones
docker compose exec app php artisan migrate:status

# Forzar migración
docker compose exec app php artisan migrate --force

# Si hay tablas corruptas, resetear (⚠️ ELIMINA DATOS)
docker compose exec app php artisan migrate:fresh --seed
```

---

### 9. Vite no funciona en desarrollo

**Síntomas:** Los assets no se actualizan automáticamente.

**Solución:**
```bash
# Verificar que el contenedor vite está corriendo
docker compose ps vite

# Ver logs de Vite
docker compose logs vite

# Reiniciar
docker compose restart vite
```

---

### 10. Imagen de Docker muy pesada

**Solución:**
```bash
# Verificar tamaño de imágenes
docker images

# Limpiar caché de Docker
docker system prune -a

# Reconstruir sin caché
docker compose build --no-cache
```

---

## Comandos de Diagnóstico

```bash
# Ver estado general
docker compose ps

# Ver logs de todos los servicios
docker compose logs --tail=50

# Verificar consumo de recursos
docker stats

# Verificar red
docker network inspect iot_network_social

# Probar conectividad interna
docker compose exec app ping mysql
docker compose exec app ping influxdb
docker compose exec app ping mosquitto

# Verificar puertos
docker compose exec app nc -zv mysql 3306
docker compose exec app nc -zv influxdb 8086
docker compose exec app nc -zv mosquitto 1883
```

---

## Logs Útiles

```bash
# Logs de Laravel
docker compose exec app tail -f /app/storage/logs/laravel.log

# Logs de Nginx
docker compose logs -f webserver

# Logs de MySQL
docker compose exec mysql tail -f /var/log/mysql/error.log

# Logs de InfluxDB
docker compose exec influxdb cat /var/log/influxdb/influxd.log

# Logs de Mosquitto
docker compose exec mosquitto cat /mosquitto/log/mosquitto.log
```
