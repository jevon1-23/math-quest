FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_pgsql pgsql mysqli

# Enable Apache modules
RUN a2enmod rewrite headers

# Enable error display
RUN echo "display_errors = On" >> /usr/local/etc/php/conf.d/error.ini \
    && echo "error_reporting = E_ALL" >> /usr/local/etc/php/conf.d/error.ini

# Copy application files
COPY . /var/www/html/

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Configure Apache
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

EXPOSE 80