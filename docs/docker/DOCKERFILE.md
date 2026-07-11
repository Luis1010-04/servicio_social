# Dockerfile — Análisis Multi-Stage

## Estructura General

El `Dockerfile` utiliza una arquitectura **multi-stage build** con 2 etapas para minimizar el tamaño de la imagen final.

```
┌─────────────────────────────────────────────────────┐
│  Etapa 1: node (node:22-alpine)                     │
│  ─────────────────────────────────────              │
│  • Instala dependencias npm                         │
│  • Compila assets con Vite + Tailwind CSS           │
│  • Genera: /app/public/build/                       │
└───────────────────────┬─────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────┐
│  Etapa 2: runtime (php:8.2-fpm-bookworm)            │
│  ──────────────────────────────────────────          │
│  • Instala extensiones PHP (pdo_mysql, gd, etc.)    │
│  • Copia vendor/ del host                           │
│  • Copia assets de la etapa node                    │
│  • Configura permisos www-data                      │
│  • Ejecuta entrypoint.sh → php-fpm                  │
└─────────────────────────────────────────────────────┘
```

## Etapa 1: Node (Build de Assets)

```dockerfile
FROM node:22-alpine AS node

WORKDIR /app
COPY package.json ./
RUN npm install --no-audit --no-fund
COPY resources/ resources/
COPY vite.config.js ./
RUN npm run build
```

**Función:** Compila los assets frontend (CSS/JS) usando Vite + Tailwind CSS.

**Archivos de salida:** `public/build/` (Assets minificados para producción).

**Optimización:** Solo `package.json` se copia antes de `npm install` para aprovechar la caché de Docker.

## Etapa 2: Runtime (PHP-FPM)

### Extensiones PHP Instaladas

| Extensión | Propósito |
|-----------|-----------|
| `pdo_mysql` | Conexión a MySQL/MariaDB |
| `mbstring` | Manejo de caracteres multibyte |
| `xml` | Procesamiento XML |
| `bcmath` | Matemáticas de precisión arbitraria |
| `zip` | Manipulación de archivos ZIP |
| `gd` | Procesamiento de imágenes |
| `intl` | Internacionalización |

### Configuración de GD

```dockerfile
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
```

Habilita soporte para imágenes JPEG y PNG con libfreetype.

### Directorios de Laravel

```dockerfile
RUN mkdir -p /app/storage/logs \
             /app/storage/framework/cache \
             /app/storage/framework/sessions \
             /app/storage/framework/views \
             /app/bootstrap/cache
```

### Permisos

```dockerfile
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache \
    && chmod -R 775 /app/storage /app/bootstrap/cache
```

El contenedor ejecuta como usuario `www-data` (no-root) por seguridad.

### Puerto

```dockerfile
EXPOSE 9000
```

PHP-FPM escucha en el puerto 9000 (conectado via fastcgi a Nginx).

## Entrypoint

```dockerfile
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["entrypoint.sh"]
CMD ["php-fpm"]
```

El `entrypoint.sh` ejecuta:
1. Espera a que MySQL esté disponible
2. Cachea configuración (solo producción)
3. Ejecuta migraciones (si `RUN_MIGRATIONS=true`)
4. Inicia PHP-FPM

## Optimizaciones

| Aspecto | Estrategia |
|---------|------------|
| **Caché de capas** | `package.json` se copia primero para cachear `npm install` |
| **Imagen ligera** | Etapa final solo incluye runtime, no Node.js |
| **Sin devDependencies** | `npm install --no-audit --no-fund` omite paquetes de desarrollo |
| **Limpieza** | `rm -rf /var/lib/apt/lists/*` elimina caché de apt |
| **Usuario no-root** | `USER www-data` reduce superficie de ataque |

## Construcción

```bash
# Construir imagen completa
docker build -t iot-project-app .

# Construir solo una etapa específica
docker build --target runtime -t iot-project-app-runtime .

# Verificar tamaño
docker images iot-project-app
```
