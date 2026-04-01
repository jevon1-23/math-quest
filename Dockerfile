FROM php:8.2-apache

# Install PostgreSQL client library and PHP extension
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo_pgsql pgsql mysqli

# Enable required Apache modules
RUN a2enmod rewrite \
    && a2enmod headers

# Enable error reporting for debugging
RUN echo "display_errors = On" >> /usr/local/etc/php/conf.d/error.ini \
    && echo "error_reporting = E_ALL" >> /usr/local/etc/php/conf.d/error.ini

# Copy your project files to Apache's web root
COPY . /var/www/html/

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Set ServerName to suppress DNS warning
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

EXPOSE 80
