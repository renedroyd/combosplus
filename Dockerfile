# Dockerfile optimizado para Dokploy
FROM dunglas/frankenphp:latest

# Instalar Node.js 20 LTS
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Instalar extensiones de PHP necesarias
RUN install-php-extensions \
    pdo_mysql pdo_pgsql pcntl posix bcmath ctype curl fileinfo \
    json mbstring tokenizer xml exif gd intl zip opcache redis \
    simplexml dom sockets

# Configurar opcache
RUN { \
        echo 'opcache.memory_consumption=256'; \
        echo 'opcache.interned_strings_buffer=32'; \
        echo 'opcache.max_accelerated_files=20000'; \
        echo 'opcache.revalidate_freq=2'; \
        echo 'opcache.fast_shutdown=1'; \
        echo 'opcache.enable_cli=1'; \
    } > /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini

# Configurar memory limit
RUN echo 'memory_limit = 512M' > /usr/local/etc/php/conf.d/memory-limit.ini

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copiar archivos de la aplicación
COPY . /app
WORKDIR /app

# Instalar dependencias PHP
RUN composer install --no-interaction --optimize-autoloader --no-dev

# Instalar dependencias Node.js y compilar assets
RUN npm ci --legacy-peer-deps && npm run build

# Limpiar archivos innecesarios
RUN rm -rf node_modules

# Configurar permisos
RUN chown -R www-data:www-data /app \
    && chmod -R 755 /app/storage \
    && chmod -R 755 /app/bootstrap/cache

# Configurar FrankenPHP para puerto 8000
ENV SERVER_NAME=":8000" \
    FRANKENPHP_CONFIG="worker ./public/index.php"

EXPOSE 8000

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \
    CMD curl -f http://localhost:8000/health || exit 1

CMD ["frankenphp", "run", "--config", "/etc/frankenphp/Caddyfile"]