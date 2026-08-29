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

# Copy app (copy full project so build doesn't fail when composer.json is absent)
COPY . .

# Install PHP deps if composer.json exists
RUN if [ -f composer.json ]; then \
      composer install --no-dev --prefer-dist --no-interaction --no-scripts --optimize-autoloader; \
    else \
      echo "composer.json not found, skipping composer install"; \
    fi

# Run composer dump-autoload if composer.json exists
RUN if [ -f composer.json ]; then \
      composer dump-autoload --optimize; \
    fi

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
  && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80
CMD ["apache2-foreground"]
