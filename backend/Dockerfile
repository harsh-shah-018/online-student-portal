FROM php:8.2-apache

# Install PDO MySQL extension
RUN docker-php-ext-install pdo pdo_mysql

# Enable Apache mod_rewrite and headers
RUN a2enmod rewrite headers

# Copy application files
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html/

# Configure Apache to allow overrides
RUN sed -i 's/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Expose port 80
EXPOSE 80
