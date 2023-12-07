# Use the official Ubuntu base image
FROM ubuntu:latest

# Use an official PHP runtime as a parent image
FROM php:7.4-fpm

# Install required extensions
RUN docker-php-ext-install dom json libxml mbstring tokenizer xml xmlwriter

# Install required dependencies
RUN apt-get update && apt-get install -y \
    curl \
    software-properties-common

# Add the ondrej/php repository
RUN add-apt-repository -y ppa:ondrej/php && \
    apt-get update

# Install PHP 7.4 and required extensions
RUN apt-get install -y \
    php8.2-fpm \
    php8.2-gd \
    php8.2-zip \
    php8.2-mysql \
    php8.2-xml \
    php8.2-pgsql \
    php8.2-curl
    

# Install Nginx and PHP dependencies
RUN apt-get install -y nginx

# Copy the custom hosts file
COPY hosts /etc/hosts-custom

# Set working directory
WORKDIR /var/www/publish/container/gateway-irk

# Copy project files
COPY . /var/www/publish/container/gateway-irk

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer 

# Install project dependencies
RUN composer update

# Set permissions for Laravel storage and bootstrap folders
RUN chown -R www-data:www-data storage bootstrap/cache
RUN chmod -R 775 storage bootstrap/cache

# Copy Nginx configuration
COPY nginx.conf /etc/nginx/sites-available/default

# Expose ports for PHP-FPM (9000), Laravel development server (8000), and HTTP (80)
EXPOSE 8050

# Start PHP-FPM and Nginx
CMD service php8.2-fpm start && nginx -g 'daemon off;'
