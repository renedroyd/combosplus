FROM dunglas/frankenphp:latest AS builder

# Instalar Node.js 20 LTS
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Instalar todas las extensiones necesarias para Laravel + FilamentPHP
RUN install-php-extensions \
    pdo_mysql pdo_pgsql pcntl posix bcmath ctype curl fileinfo json \
    mbstring tokenizer xml exif gd imagick intl zip opcache redis \
    simplexml dom sockets

# Configuración de opcache
RUN { \
        echo 'opcache.memory_consumption=256'; \
        echo 'opcache.interned_strings_buffer=32'; \
        echo 'opcache.max_accelerated_files=20000'; \
        echo 'opcache.revalidate_freq=2'; \
        echo 'opcache.fast_shutdown=1'; \
        echo 'opcache.enable_cli=1'; \
    } > /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini

RUN echo 'memory_limit = 512M' > /usr/local/etc/php/conf.d/memory-limit.ini

# Copiar archivos
COPY --chown=www-data:www-data . /app
WORKDIR /app

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Instalar dependencias PHP
RUN composer install --no-interaction --optimize-autoloader --no-dev

# Instalar dependencias Node.js y compilar assets
RUN npm ci --legacy-peer-deps && npm run build

# --- Fase final ---
FROM dunglas/frankenphp:latest

# Instalar extensiones (misma lista que arriba)
RUN install-php-extensions \
    pdo_mysql pdo_pgsql pcntl posix bcmath ctype curl fileinfo json \
    mbstring tokenizer xml exif gd imagick intl zip opcache redis \
    simplexml dom sockets

# Copiar configuración de PHP desde builder
COPY --from=builder /usr/local/etc/php/conf.d/*.ini /usr/local/etc/php/conf.d/

# Copiar la aplicación compilada
COPY --from=builder --chown=www-data:www-data /app /app
COPY --from=builder --chown=www-data:www-data /usr/local/bin/frankenphp /usr/local/bin/frankenphp

WORKDIR /app

# Configurar FrankenPHP para puerto 8000
ENV SERVER_NAME=":8000" \
    FRANKENPHP_CONFIG="worker ./public/index.php"

EXPOSE 8000

CMD ["frankenphp", "run", "--config", "/etc/frankenphp/Caddyfile"]
