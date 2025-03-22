FROM php:8.2-fpm

# Install necessary system packages
RUN apt-get update && apt-get install -y \
    nginx zip unzip git curl libpng-dev libonig-dev libxml2-dev \
    procps libpq-dev \
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
