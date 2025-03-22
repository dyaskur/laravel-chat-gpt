FROM php:8.2-fpm-alpine

# Install necessary system packages
RUN apk add --no-cache \
    nginx zip unzip git curl libpng-dev libxml2-dev libpq-dev \
    bash oniguruma-dev pkgconf \
    && docker-php-ext-install mbstring exif pcntl bcmath gd pdo_pgsql

# Set working directory
WORKDIR /var/www/html

# Copy Laravel files
COPY . .

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-dev --optimize-autoloader

# Set permissions
RUN chmod -R 777 storage bootstrap/cache

# Copy NGINX config
COPY nginx.conf /etc/nginx/nginx.conf

# Expose port 8080 (required by Cloud Run)
EXPOSE 8080

# Start both PHP-FPM and NGINX
CMD php-fpm -D && nginx -g 'daemon off;'
