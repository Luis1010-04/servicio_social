#!/bin/bash
set -e

echo "=========================================="
echo " IoT_Project — Entry Point"
echo "=========================================="

# ----------------------------------------------------------------------------
# 1. Esperar a que MySQL esté disponible
# ----------------------------------------------------------------------------
echo "[1/4] Esperando a que MySQL esté disponible..."

MAX_ATTEMPTS=30
ATTEMPT=1

while [ $ATTEMPT -le $MAX_ATTEMPTS ]; do
    if php artisan db:monitor 2>/dev/null; then
        echo "✓ MySQL está disponible"
        break
    fi
    
    echo "  Intento $ATTEMPT/$MAX_ATTEMPTS - MySQL no disponible, esperando..."
    sleep 2
    ATTEMPT=$((ATTEMPT + 1))
done

if [ $ATTEMPT -gt $MAX_ATTEMPTS ]; then
    echo "✗ Error: MySQL no estuvo disponible después de $MAX_ATTEMPTS intentos"
    exit 1
fi

# ----------------------------------------------------------------------------
# 2. Generar APP_KEY si no existe
# ----------------------------------------------------------------------------
echo "[2/4] Verificando APP_KEY..."

if [ -z "$APP_KEY" ]; then
    echo "  Generando APP_KEY..."
    php artisan key:generate --force
fi

# ----------------------------------------------------------------------------
# 3. Cachear configuración (solo en producción)
# ----------------------------------------------------------------------------
echo "[3/4] Cachear configuración..."

if [ "$APP_ENV" = "production" ]; then
    echo "  Modo producción: cacheando config, rutas y vistas..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    echo "✓ Configuración cacheada"
else
    echo "  Modo desarrollo: omitiendo cache"
fi

# ----------------------------------------------------------------------------
# 4. Ejecutar migraciones (condicional)
# ----------------------------------------------------------------------------
echo "[4/4] Verificando migraciones..."

if [ "$RUN_MIGRATIONS" = "true" ]; then
    echo "  Ejecutando migraciones..."
    php artisan migrate --force
    echo "✓ Migraciones completadas"
else
    echo "  Migraciones desactivadas (RUN_MIGRATIONS != true)"
fi

echo "=========================================="
echo " ✓ Iniciando PHP-FPM"
echo "=========================================="

# Ejecutar el comando principal (PHP-FPM)
exec "$@"
