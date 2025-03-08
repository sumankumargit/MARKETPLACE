# Use PHP with Apache
FROM php:8.1-apache

# Set working directory inside the container
WORKDIR /var/www/html

# Install required PHP extensions
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Enable Apache mod_rewrite for CakePHP
RUN a2enmod rewrite

# Copy application files
COPY . /var/www/html

# Ensure necessary directories exist
RUN mkdir -p /var/www/html/tmp /var/www/html/logs \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/tmp /var/www/html/logs

# Expose port 80
EXPOSE 80

# Start Apache server
CMD ["apache2-foreground"]
