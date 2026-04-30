# Etapa 1: Construcción de dependencias y assets (Frontend y Backend)
FROM php:8.3-cli AS builder

# Instalar dependencias del sistema necesarias
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd zip pdo pdo_mysql

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Instalar Node.js y pnpm (según requerimiento en composer.json)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && npm install -g pnpm

# Establecer directorio de trabajo
WORKDIR /app

# Copiar archivos de configuración primero para optimizar la caché de Docker
COPY composer.json composer.lock ./
COPY package.json pnpm-lock.yaml ./
COPY vite.config.js ./

# Instalar dependencias de PHP y Node.js (se ejecuta en paralelo para optimizar tiempo)
RUN composer install --no-dev --no-scripts --prefer-dist
RUN pnpm install

# Copiar el resto del código
COPY . .

# Generar assets del frontend (Vite) y optimizar autoloader
RUN pnpm run build
RUN composer dump-autoload --optimize

# ==============================================================================

# Etapa 2: Imagen final de producción (Más ligera y segura)
FROM php:8.3-apache

# Instalar extensiones de PHP necesarias para producción
RUN apt-get update && apt-get install -y \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd zip pdo pdo_mysql \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Habilitar mod_rewrite de Apache para Laravel (rutas amigables)
RUN a2enmod rewrite

# Cambiar el DocumentRoot de Apache a la carpeta public de Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Establecer directorio de trabajo
WORKDIR /var/www/html

# Copiar la aplicación compilada desde la etapa anterior (builder)
COPY --from=builder /app /var/www/html

# Ajustar los permisos para que Laravel pueda escribir en logs y cache
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Exponer el puerto 80 al contenedor
EXPOSE 80

# El comando por defecto de php:apache ya inicia el servidor web
