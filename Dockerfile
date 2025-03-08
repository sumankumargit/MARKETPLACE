# Use official PHP with Apache
FROM php:8.1-apache

# Set working directory inside the container
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    zip unzip \
    curl \
    git \
    && docker-php-ext-install pdo pdo_mysql mysqli gd zip

# Enable Apache mod_rewrite for CakePHP
RUN a2enmod rewrite

# Install Composer globally
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application files
COPY . /var/www/html

# Ensure necessary directories exist
RUN mkdir -p /var/www/html/tmp /var/www/html/logs \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/tmp /var/www/html/logs

# Install PHP dependencies using Composer
RUN composer install --no-dev --prefer-dist --optimize-autoloader

# Copy environment file
COPY .env /var/www/html/.env

# Expose port 80
EXPOSE 80

# Start Apache server
CMD ["apache2-foreground"]
