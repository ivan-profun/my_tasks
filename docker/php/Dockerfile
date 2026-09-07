FROM php:8.4-fpm-alpine

RUN apk add --no-cache \
    bash \
    git \
    curl \
    unzip \
    libzip-dev \
    oniguruma-dev \
    postgresql-dev \
    icu-dev \
    curl-dev \
    autoconf \
    g++ \
    make

RUN docker-php-ext-install \
        pdo_pgsql \
        mbstring \
        bcmath \
        zip \
        intl \
        curl \
        opcache

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY php.ini /usr/local/etc/php/conf.d/custom.ini

COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["entrypoint.sh"]

WORKDIR /var/www/html

EXPOSE 9000

CMD ["php-fpm"]