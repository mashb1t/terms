FROM php:8.0-fpm

COPY image-files/usr/local/bin/docker-image-cleanup /usr/local/bin/docker-image-cleanup

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

COPY image-files/ /

WORKDIR /var/www/html

# node
# ---
ENV NODE_VERSION=14

RUN curl -sL "https://deb.nodesource.com/setup_${NODE_VERSION}.x" | bash - \
  && apt-get install -y nodejs

RUN npm ci
RUN npm run production

# composer
# ---
COPY --from=composer:2.0 /usr/bin/composer /usr/local/bin/composer
RUN composer install --optimize-autoloader --no-dev

#RUN chown -R www-data:www-data /var/www
#RUN chmod -R 777 /var/www
