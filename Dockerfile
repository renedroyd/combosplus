FROM dunglas/frankenphp:latest AS builder

# Instalar todas las extensiones necesarias para Laravel + FilamentPHP
RUN install-php-extensions \
    # Extensiones base de Laravel
    pdo_mysql \
    pdo_pgsql \
    pcntl \
    posix \
    bcmath \
    ctype \
    curl \
    fileinfo \
    json \
    mbstring \
    tokenizer \
    xml \
    # Extensiones para FilamentPHP
    exif \
    gd \
    imagick \
    intl \
    zip \
    # Extensiones para rendimiento y caché
    opcache \
    redis \
    # Extensiones para procesamiento de imágenes y PDFs
    # (útiles para campos de imagen y exportaciones)
    # gd ya está incluido arriba
    # Extensiones adicionales recomendadas
    simplexml \
    dom \
    # Para colas y jobs
    pcntl \
    # Para WebSockets (si usas Filament con Livewire)
    sockets

# Configuración de opcache para máximo rendimiento
RUN { \
        echo 'opcache.memory_consumption=256'; \
        echo 'opcache.interned_strings_buffer=32'; \
        echo 'opcache.max_accelerated_files=20000'; \
        echo 'opcache.revalidate_freq=2'; \
        echo 'opcache.fast_shutdown=1'; \
        echo 'opcache.enable_cli=1'; \
    } > /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini

# Configuración de memory limit para Filament (manejo de archivos grandes)
RUN echo 'memory_limit = 512M' > /usr/local/etc/php/conf.d/memory-limit.ini

# Copiar archivos de la aplicación
COPY --chown=www-data:www-data . /app
WORKDIR /app

# Instalar composer y dependencias de PHP
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Instalar dependencias (incluyendo filament/filament)
RUN composer install --no-interaction --optimize-autoloader --no-dev

# --- Fase final ---
FROM dunglas/frankenphp:latest

# Instalar las mismas extensiones en la fase final
RUN install-php-extensions \
    pdo_mysql \
    pdo_pgsql \
    pcntl \
    posix \
    bcmath \
    ctype \
    curl \
    fileinfo \
    json \
    mbstring \
    tokenizer \
    xml \
    exif \
    gd \
    imagick \
    intl \
    zip \
    opcache \
    redis \
    simplexml \
    dom \
    sockets

# Copiar configuración de PHP
COPY --from=builder /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini
COPY --from=builder /usr/local/etc/php/conf.d/memory-limit.ini /usr/local/etc/php/conf.d/memory-limit.ini

# Copiar la aplicación desde la fase builder
COPY --from=builder --chown=www-data:www-data /app /app
WORKDIR /app

# Variables de entorno para Octane + FrankenPHP
ENV SERVER_NAME=":80" \
    FRANKENPHP_CONFIG="worker ./public/index.php" \
    OCTANE_SERVER="frankenphp"

# Instalar supervisor para manejar procesos (opcional, útil para colas)
RUN apt-get update && apt-get install -y supervisor \
    && rm -rf /var/lib/apt/lists/*

# Configurar supervisor para ejecutar Octane y Laravel Queue Worker
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

EXPOSE 8081

# Usar supervisor para manejar múltiples procesos
CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/supervisord.conf"]
