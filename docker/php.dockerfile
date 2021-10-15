FROM php:8.0-fpm

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN chmod +x /usr/local/bin/install-php-extensions && sync \
    && install-php-extensions bcmath pdo_mysql zip exif pcntl gd

# Install dependencies
RUN apt-get update && apt-get install -y \
    ca-certificates \
    libmcrypt-dev \
    libzip-dev \
    libcap2-bin \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    jpegoptim optipng pngquant gifsicle \
    mariadb-client \
    jq \
    zip \
    ffmpeg \
    unzip \
    curl

# Clear cache
RUN apt-get autoremove && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN pecl install xdebug \
    && pecl clear-cache \

RUN docker-php-ext-enable xdebug

WORKDIR /var/www/html