# ============================================================================
# IoT_Project — Dockerfile Multi-Stage
# Laravel 12.x + PHP 8.2-FPM + Node (build assets)
# ============================================================================

# ----------------------------------------------------------------------------
# Etapa 1: Node — Build de assets (Vite + Tailwind CSS)
# ----------------------------------------------------------------------------
FROM node:22-alpine AS node

WORKDIR /app

# Copiar archivos de dependencias npm
COPY package.json ./

# Instalar dependencias npm
RUN npm install --no-audit --no-fund

# Copiar fuente y construir assets
COPY resources/ resources/
COPY vite.config.js ./
RUN npm run build

# ----------------------------------------------------------------------------
# Etapa 2: Runtime — Imagen final con PHP-FPM (Debian)
# ----------------------------------------------------------------------------
FROM php:8.2-fpm-bookworm AS runtime

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    bash \
    curl \
    unzip \
    libicu-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring xml bcmath zip gd \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app

# Copiar vendor local (pre-instalado en host)
COPY vendor/ vendor/
COPY composer.json composer.lock ./

# Copiar assets construidos de la etapa node
# Nota: Asegúrate de crear el directorio público si no se copia completo en el paso inferior
COPY --from=node /app/public/build /app/public/build

# Copiar código fuente de la aplicación
COPY . /app

# Crear directorios necesarios de la estructura de Laravel
RUN mkdir -p /app/storage/logs \
             /app/storage/framework/cache \
             /app/storage/framework/sessions \
             /app/storage/framework/views \
             /app/bootstrap/cache

# Configurar permisos para el usuario nativo de Debian (www-data)
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache \
    && chmod -R 775 /app/storage /app/bootstrap/cache

# Copiar entrypoint
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Exponer puerto PHP-FPM
EXPOSE 9000

# Cambiar al usuario no-root nativo de PHP-FPM en Debian
USER www-data

# Ejecutar entrypoint
ENTRYPOINT ["entrypoint.sh"]

# Comando por defecto: PHP-FPM
CMD ["php-fpm"]