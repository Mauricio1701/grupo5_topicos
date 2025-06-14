FROM php:8.2-fpm-alpine

RUN apk update && apk add --no-cache \
    bash \
    libpng \
    libjpeg-turbo \
    freetype \
    libzip \
    libxml2 \
    libxslt \
    libjpeg-turbo-dev \
    freetype-dev \
    libpng-dev \
    libzip-dev \
    libxml2-dev \
    libxslt-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd zip exif pcntl bcmath pdo pdo_mysql \
    && apk del libjpeg-turbo-dev freetype-dev libpng-dev libzip-dev libxml2-dev libxslt-dev

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

RUN composer install --no-dev --optimize-autoloader

EXPOSE 9000

CMD ["php-fpm"]