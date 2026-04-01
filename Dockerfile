FROM php:8.2-apache

# Install mysqli extension for MySQL
RUN docker-php-ext-install mysqli

# Enable mod_rewrite for Apache
RUN a2enmod rewrite

# Copy your project files to Apache's web root
COPY . /var/www/html/

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html

# Configure Apache to use index.php
RUN echo "DirectoryIndex index.php" >> /etc/apache2/apache2.conf

EXPOSE 80
