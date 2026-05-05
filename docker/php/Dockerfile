FROM php:8.3-fpm-alpine

RUN apk add --no-cache \
        bash \
        $PHPIZE_DEPS \
    && docker-php-ext-install opcache pdo_mysql \
    && apk del $PHPIZE_DEPS

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
COPY docker/php/conf.d/zz-app.ini /usr/local/etc/php/conf.d/zz-app.ini

WORKDIR /var/www/app
