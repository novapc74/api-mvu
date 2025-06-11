FROM php:8.4-fpm-alpine

RUN mv $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini

COPY ./embed/php/conf.d $PHP_INI_DIR/conf.d

RUN apk update && apk add --no-cache \
        git \
        g++ \
        bash \
        make \
        wget \
        unzip \
        libtool \
        icu-dev \
        autoconf \
        pkgconfig \
        libpng-dev \
        libzip-dev \
        libwebp-dev \
        freetype-dev \
    && docker-php-ext-install pdo pdo_mysql zip intl \
    && rm -rf /var/cache/apk/*

WORKDIR /project

ARG UID=1000
RUN adduser -D -u $UID -s /bin/bash develop && \
    echo "develop:develop" | chpasswd && \
    chown -R develop:develop /project

USER develop