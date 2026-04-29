FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    libzip-dev \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl

RUN docker-php-ext-install \
    gd \
    zip \
    xml \
    pdo \
    pdo_mysql

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

RUN composer install --optimize-autoloader --no-interaction

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8080"]