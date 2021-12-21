FROM composer:2 as composer

FROM php:8.0-fpm
# Fix debconf warnings upon build
ARG DEBIAN_FRONTEND=noninteractive
ENV NODE_VERSION 12.13.0

# Install selected extensions and other stuff
RUN apt-get update \
    && apt-get install -y git bash wget curl gnupg libbz2-dev zlib1g-dev zip libzip-dev libpng-dev libicu-dev libtidy-dev libssl-dev libcurl4-openssl-dev

RUN docker-php-ext-install opcache pdo pdo_mysql bz2 gd intl tidy tokenizer zip
RUN pecl install redis && docker-php-ext-enable redis
RUN yes | pecl install swoole && docker-php-ext-enable swoole

# Install Nodejs and Yarn
RUN wget https://deb.nodesource.com/setup_12.x  | bash -
RUN apt-get -y install nodejs npm
RUN npm install yarn --global

COPY --from=composer /usr/bin/composer /usr/bin/composer

WORKDIR "/application"
