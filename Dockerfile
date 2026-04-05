FROM php:8.2-apache

# Install PostgreSQL client library and PHP extensions
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo_pgsql pgsql mysqli

# Enable Apache modules
RUN a2enmod rewrite headers

# Disable display_errors in production — errors go to log only
RUN echo "display_errors = Off" >> /usr/local/etc/php/conf.d/error.ini \
    && echo "log_errors = On" >> /usr/local/etc/php/conf.d/error.ini \
    && echo "error_reporting = E_ALL" >> /usr/local/etc/php/conf.d/error.ini

# Set ServerName
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Copy application files
COPY . /var/www/html/

# Set permissions
RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html

EXPOSE 80