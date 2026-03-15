FROM php:8.4-cli

WORKDIR /var/www

# install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev

# install php extensions
RUN docker-php-ext-install pdo pdo_mysql zip

# install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# copy project
COPY . .

# install laravel dependencies
RUN composer install --no-dev --optimize-autoloader

# generate key
RUN php artisan key:generate

EXPOSE 10000

CMD php artisan serve --host=0.0.0.0 --port=10000
