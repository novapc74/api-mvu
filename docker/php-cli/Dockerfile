FROM php:8.4-alpine

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

ENV COMPOSER_ALLOW_SUPERUSER 1

WORKDIR /project

ARG UID=1000
RUN adduser -D -u $UID -s /bin/bash develop && \
    echo "develop:develop" | chpasswd && \
    chown -R develop:develop /project

USER develop

RUN mkdir -p /home/develop/bin

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/home/develop/bin --filename=composer --quiet

ENV PATH="/home/develop/bin:${PATH}"