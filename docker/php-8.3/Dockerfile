FROM php:8.3-fpm-alpine

WORKDIR /var/www/html

RUN apk add --no-cache --virtual .build-deps \
        postgresql-dev \
    && apk add --no-cache postgresql-client \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install \
        pdo \
        pdo_pgsql \
        pgsql \
    && curl -s https://getcomposer.org/installer | \
        php -- --install-dir=/usr/local/bin/ --filename=composer \
    && apk del -f .build-deps
