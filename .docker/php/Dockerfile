FROM php:8.4-fpm-alpine AS base

ENV NODE_VERSION=22.0.0

ARG WWWUSER
ARG WWWGROUP

RUN echo "https://dl-cdn.alpinelinux.org/alpine/edge/community" >> /etc/apk/repositories \
    && echo "https://dl-cdn.alpinelinux.org/alpine/edge/main" >> /etc/apk/repositories \
    && apk update && apk upgrade

RUN set -eux; \
    apk add --no-cache --virtual .gosu-deps dpkg; \
    GOSU_VERSION=1.17; \
    wget -O /usr/local/bin/gosu "https://github.com/tianon/gosu/releases/download/$GOSU_VERSION/gosu-amd64"; \
    chmod +x /usr/local/bin/gosu; \
    apk del .gosu-deps


RUN apk add --no-cache su-exec \
    bash \
    zip \
    unzip \
    git \
    curl \
    supervisor \
    tzdata \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    postgresql-dev \
    rabbitmq-c-dev \
    linux-headers \
    libzip-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql pdo_pgsql opcache bcmath sockets pcntl exif intl zip

RUN apk add --no-cache nodejs \
    npm

RUN node -v && npm -v

RUN apk add --no-cache autoconf \
  g++ \
  make \
  libtool

RUN pecl install redis amqp msgpack \
    && docker-php-ext-enable redis amqp msgpack

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY .docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini

RUN mkdir -p /var/log/supervisor \
    && touch /var/log/supervisord.log /var/log/supervisord.err \
    && chown -R www-data:www-data /var/log/supervisor /var/log/supervisord.* \
    && chmod -R 777 /var/log/supervisor /var/log/supervisord.*

COPY .docker/php/entrypoint.sh /entrypoint.sh

RUN apk add --no-cache shadow \
    && groupmod -g ${WWWGROUP} www-data \
    && usermod -u ${WWWUSER} www-data \
    && usermod -aG www-data www-data


RUN chmod +x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]

FROM base AS dev

RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.start_with_request=trigger" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.idekey=PHPSTORM" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_port=9003" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && apk del .build-deps

CMD ["supervisord", "-c", "/etc/supervisord.conf"]

FROM base AS prod

WORKDIR /var/www

CMD ["supervisord", "-c", "/etc/supervisord.conf"]

EXPOSE 9000
