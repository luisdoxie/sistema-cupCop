FROM php:8.2-apache

# Extensiones del sistema necesarias
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev \
    libzip-dev libpq-dev zip unzip \
    && docker-php-ext-install \
        pdo pdo_pgsql pgsql \
        mbstring bcmath gd zip intl exif pcntl \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

# Node.js para compilar assets con Vite
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copiar código fuente
COPY . .

# Instalar dependencias PHP y compilar assets
RUN composer install --prefer-dist --no-dev --optimize-autoloader --no-interaction
RUN npm install && npm run build

# Permisos de storage
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Apuntar Apache a /public
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
        /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' \
        /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Script de inicio
COPY docker-start.sh /usr/local/bin/docker-start.sh
RUN chmod +x /usr/local/bin/docker-start.sh

EXPOSE 80

CMD ["/usr/local/bin/docker-start.sh"]
