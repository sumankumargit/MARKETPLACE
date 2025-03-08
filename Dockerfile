# Use an official PHP Apache image with necessary extensions
FROM php:8.1-apache

# Set the working directory inside the container
WORKDIR /var/www/html

# Install required PHP extensions
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd zip pdo pdo_mysql mysqli

# Enable Apache mod_rewrite for CakePHP pretty URLs
RUN a2enmod rewrite

# Install Composer globally
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application files to the container
COPY . /var/www/html

# Set proper permissions for the CakePHP app
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/tmp \
    && chmod -R 775 /var/www/html/logs

# Install dependencies using Composer
RUN composer install --no-dev --optimize-autoloader

# Expose port 80 for Apache
EXPOSE 80

# Start Apache service
CMD ["apache2-foreground"]
