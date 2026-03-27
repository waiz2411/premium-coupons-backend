# ===========================
# 1) Build stage
# ===========================
FROM php:8.2-fpm AS build

# System dependencies
RUN apt-get update && apt-get install -y \
    unzip zip git libpng-dev libonig-dev libxml2-dev libzip-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring gd zip bcmath

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy entire Laravel project
COPY . .

# Install dependencies (NO dev)
RUN composer install --no-dev --optimize-autoloader

# Generate optimized Laravel files
RUN php artisan config:cache
RUN php artisan route:cache
RUN php artisan view:cache


# ===========================
# 2) Final stage
# ===========================
FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    unzip zip git libpng-dev libonig-dev libxml2-dev libzip-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring gd zip bcmath

WORKDIR /app

# Copy build assets
COPY --from=build /app /app

EXPOSE 8080

# Render requires listening on port 8080
CMD php -S 0.0.0.0:8080 -t public