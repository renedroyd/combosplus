FROM dunglas/frankenphp:latest

# Instalar Node.js 20 LTS
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean

# Instalar extensiones PHP
RUN install-php-extensions \
    pdo_mysql pdo_pgsql bcmath ctype curl fileinfo \
    json mbstring tokenizer xml gd intl zip opcache redis

# Configurar PHP
RUN echo 'memory_limit = 512M' > /usr/local/etc/php/conf.d/memory-limit.ini

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copiar aplicación
COPY . /app
WORKDIR /app

# Instalar dependencias
RUN composer install --no-interaction --optimize-autoloader --no-dev

# Compilar assets (si existen)
RUN if [ -f "package.json" ]; then \
        npm ci --legacy-peer-deps && npm run build || true; \
    fi

# Configurar permisos
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache

# Configurar FrankenPHP (SIN modo worker para evitar errores)
ENV SERVER_NAME=":8000"

EXPOSE 8000

# Comando de inicio tradicional
CMD ["frankenphp", "run"]
