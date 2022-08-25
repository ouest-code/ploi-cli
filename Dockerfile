ARG PHP_VERSION=8.1

FROM webdevops/php:$PHP_VERSION as builder

WORKDIR /app

COPY composer.json composer.json
COPY composer.lock composer.lock

RUN composer install --quiet --no-ansi --no-interaction --no-scripts --no-suggest --no-progress --prefer-dist

FROM php:$PHP_VERSION-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    tini \
    zip \
    unzip \
    make \
    libcurl4-openssl-dev \
    libzip-dev

# Install PHP extensions
RUN docker-php-ext-install zip curl

# Clean cache
RUN apt-get -y autoremove \
        && apt-get clean \
        && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

WORKDIR /app

# Copy source code
COPY --chown=www-data:www-data . /app

# Copy vendor
COPY --from=builder --chown=www-data:www-data /app/vendor/ /app/vendor/

COPY docker-entrypoint.sh /docker-entrypoint.sh

ENTRYPOINT ["/docker-entrypoint.sh"]

CMD ["php", "ploi"]
