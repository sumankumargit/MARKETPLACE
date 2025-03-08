# Use official PHP image with Apache
FROM php:8.1-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Install PHP extensions needed for PHPMailer
RUN apt-get install -y libssl-dev libcurl4-openssl-dev && docker-php-ext-install sockets

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy only composer files first (for better caching)
COPY composer.json composer.lock ./

# Install dependencies first to leverage caching
RUN composer install --no-dev --optimize-autoloader

# Now copy rest of the application files
COPY . .

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
