# ============================================================================
# IoT_Project — Dockerfile Multi-Stage
# Laravel 12.x + PHP 8.2-FPM + Node (build assets)
# ============================================================================

# ----------------------------------------------------------------------------
# Etapa 1: Node — Build de assets (Vite + Tailwind CSS)
# ----------------------------------------------------------------------------
FROM node:18-alpine AS node

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
    && docker-php-ext-enable curl fileinfo tokenizer json \
    && rm -rf /var/lib/apt/lists/*

# Configurar PHP-FPM
RUN sed -i 's/user = nobody/user = www/' /usr/local/etc/php-fpm.d/www.conf \
    && sed -i 's/group = nobody/group = www/' /usr/local/etc/php-fpm.d/www.conf \
    && sed -i 's/listen = 127.0.0.1:9000/listen = 0.0.0.0:9000/' /usr/local/etc/php-fpm.d/www.conf

# Crear usuario www (uid 1000)
RUN addgroup --gid 1000 www \
    && adduser --uid 1000 --gid 1000 --disabled-password --gecos "" www

WORKDIR /app

# Copiar vendor local (pre-instalado en host)
COPY vendor/ vendor/
COPY composer.json composer.lock ./

# Copiar assets construidos de la etapa node
COPY --from=node /app/public/build /app/public/build

# Copiar código fuente de la aplicación
COPY . /app

# Configurar permisos para storage y bootstrap/cache
RUN chown -R www:www /app/storage /app/bootstrap/cache \
    && chmod -R 775 /app/storage /app/bootstrap/cache

# Crear directorios de log necesarios
RUN mkdir -p /app/storage/logs /app/storage/framework/cache /app/storage/framework/sessions \
    && chown -R www:www /app/storage/logs /app/storage/framework

# Copiar entrypoint
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Exponer puerto PHP-FPM
EXPOSE 9000

# Cambiar a usuario no-root
USER www

# Ejecutar entrypoint
ENTRYPOINT ["entrypoint.sh"]

# Comando por defecto: PHP-FPM
CMD ["php-fpm"]
