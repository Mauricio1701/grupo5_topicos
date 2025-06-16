# Utilizar la imagen de PHP 8.2 con Apache
FROM php:8.2-apache

# Instalar dependencias del sistema para PHP y Vite
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    git \
    unzip \
    libzip-dev \
    curl \
    libicu-dev \
    zlib1g-dev \
    nodejs \
    npm \
    && docker-php-ext-configure zip \
    && docker-php-ext-install gd zip intl pdo pdo_mysql

# Instalar Composer (gestor de dependencias PHP)
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Establecer el directorio de trabajo
WORKDIR /var/www/html

# Copiar los archivos del proyecto Laravel al contenedor
COPY . .

# Instalar las dependencias de PHP (Laravel)
RUN composer install --no-interaction --prefer-dist

# Instalar las dependencias de JavaScript (Vite y otros paquetes npm)
RUN npm install

# Configurar Vite para producción
RUN npm run build

RUN chown -R www-data:www-data /var/www/html/public && \
    chmod -R 755 /var/www/html/public

RUN chown -R www-data:www-data /var/www/html/storage && \
    chmod -R 755 /var/www/html/storage

RUN php artisan storage:link

# Exponer el puerto 80
EXPOSE 80

# Habilitar el módulo de Apache para la reescritura de URL
RUN a2enmod rewrite

RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|' /etc/apache2/sites-available/000-default.conf

# Iniciar Apache
CMD ["apache2-foreground"]