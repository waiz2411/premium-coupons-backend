# ===============================
# Laravel Dockerfile for Render
# ===============================

FROM php:8.2-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    unzip \
    zip \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring gd zip bcmath

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy entire Laravel project
COPY . .

# Install Laravel dependencies
RUN composer install --no-dev --optimize-autoloader

# Laravel optimization
RUN php artisan config:cache || true
RUN php artisan route:cache || true
RUN php artisan view:cache || true

# Storage permissions fix
RUN chmod -R 775 storage bootstrap/cache

# Render requires port 8080
EXPOSE 8080

# Run migrations + start server automatically
CMD php artisan migrate --force && php -S 0.0.0.0:8080 -t public