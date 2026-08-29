# Use official PHP Apache image
FROM php:8.2-apache

# System deps
RUN apt-get update \
  && apt-get install -y --no-install-recommends \
    git curl libpng-dev libjpeg-dev libfreetype6-dev libonig-dev libxml2-dev zip unzip libzip-dev libpq-dev \
  && rm -rf /var/lib/apt/lists/*

# Enable apache rewrite
RUN a2enmod rewrite

# Configure gd and other extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
  && docker-php-ext-install -j$(nproc) gd mbstring exif pcntl bcmath zip sockets pdo pdo_mysql pdo_pgsql

# Install composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working dir
WORKDIR /var/www/html

# Copy composer files first to leverage Docker cache
COPY composer.json composer.lock ./

# Install PHP deps
RUN composer install --no-dev --prefer-dist --no-interaction --no-scripts --optimize-autoloader

# Copy app
COPY . .

# Run composer scripts (if you need migrations/optimize)
RUN composer dump-autoload --optimize

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
  && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80
CMD ["apache2-foreground"]
