FROM node:14 as terms-node

COPY php/ /

WORKDIR /var/www/html

RUN npm ci
RUN npm run production


FROM php:8.0-fpm as terms-php

COPY php/usr/local/bin/docker-image-cleanup /usr/local/bin/docker-image-cleanup

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

RUN apt-get update \
    && apt-get install -y  \
        # curl \
        libcurl4-openssl-dev \
        # gd
        libpng-dev \
        libjpeg-dev \
        libfreetype6-dev \
        # imagick \
        libmagickwand-dev \
        libmagickcore-dev \
        # zip
        libzip-dev \
        libicu-dev \
        # misc \
        unzip  \
        vim \
        zip  \
    && install-php-extensions bcmath curl gd imagick intl json opcache pdo pdo_mysql redis zip \
    && sh /usr/local/bin/docker-image-cleanup

COPY php/ /

WORKDIR /var/www/html

# composer
# ---
COPY --from=composer:2.0 /usr/bin/composer /usr/local/bin/composer
RUN composer install --optimize-autoloader --no-dev

# node
# ---
COPY --from=terms-node /var/www/html/public /var/www/html/public
COPY --from=terms-node /var/www/html/node_modules /var/www/html/node_modules

# laravel
# ---
RUN php artisan storage:link

# permissions
# ---
RUN chown -R www-data:www-data /var/www/html \
    && chown -R www-data:www-data /var/www/html/storage \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/public \
    && chmod -R 775 /var/www/html/bootstrap/cache


FROM nginx:1.21 as terms-nginx

COPY --from=terms-php /var/www/html/public /var/www/html/public

COPY nginx/etc/nginx/ /etc/nginx/

RUN cd /var/www/html/public \
    # clear copied storage symlink
    && rm storage \
    && ln -s /var/www/html/storage-nginx/app/public storage
