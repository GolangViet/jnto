FROM php:8.1-fpm
WORKDIR /var/www

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        acl \
        git \
        g++ \
        libcurl4-openssl-dev \
        libfreetype6-dev \
        libicu-dev \
        libjpeg-dev \
        libpng-dev \
        libpq-dev \
        libssl-dev \
        libwebp-dev \
        libxml2-dev \
        libzip-dev \
        pkg-config \
        procps \
        supervisor \
        unzip \
        zip \
        zlib1g-dev \
    && docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
        --with-webp \
    && docker-php-ext-install -j"$(nproc)" \
        gd \
        intl \
        opcache \
        pdo_mysql \
        pdo_pgsql \
        xml \
        zip \
    && curl -fsSL https://pecl.php.net/get/apcu-5.1.28.tgz -o /tmp/apcu.tgz \
    && pecl install /tmp/apcu.tgz \
    && docker-php-ext-enable apcu \
    && rm -f /tmp/apcu.tgz \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*
# RUN apt-get install -y nodejs npm
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
