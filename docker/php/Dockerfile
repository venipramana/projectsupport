FROM php:fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nodejs \
    npm \
    $PHPIZE_DEPS

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Redis extension via PECL
RUN pecl install redis && docker-php-ext-enable redis

# Copy PHP-FPM and OPCache custom configurations
COPY www.conf /usr/local/etc/php-fpm.d/www.conf
COPY opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/backend

# Optional: Add user for laravel application to avoid permission issues
RUN groupadd -g 1000 www
RUN useradd -u 1000 -ms /bin/bash -g www www
USER www
