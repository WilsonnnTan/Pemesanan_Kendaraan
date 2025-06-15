# Use the official PHP 8.2 with Apache base image
FROM php:8.2-apache

# Set the ServerName to suppress the Apache startup warning
# This is a good practice and harmless.
COPY conf/servername.conf /etc/apache2/conf-available/servername.conf
RUN a2enconf servername

# Install system dependencies and PHP extensions
# Added `libonig-dev` for `mbstring` which is commonly used.
RUN apt-get update && apt-get install -y --no-install-recommends \
    libicu-dev \
    libzip-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libonig-dev \
    zip \
    unzip \
    # Clean up apt caches to reduce image size
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) intl pdo pdo_mysql gd zip mbstring

# Enable Apache's mod_rewrite
RUN a2enmod rewrite

# Change DocumentRoot to the 'public' folder (CodeIgniter 4)
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|' /etc/apache2/sites-available/000-default.conf

# Allow .htaccess to override Apache settings
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy source code into the container
# Ensure you have a .dockerignore file to exclude unnecessary files like .git, node_modules, etc.
COPY . /var/www/html

# Set working directory
WORKDIR /var/www/html

# Install PHP dependencies with Composer
# --no-dev is good for production.
RUN composer install --no-interaction --no-dev --optimize-autoloader

# --- CRITICAL FOR RAILWAY DEBUGGING ---
# Set CI_ENVIRONMENT based on an environment variable, defaulting to 'production'
# You can set CI_ENVIRONMENT=development in Railway's environment variables for debugging.
# This makes your image adaptable.
ENV CI_ENVIRONMENT production

# Configure Apache's logging to stderr (good for Docker/Railway)
# By default, php:apache sends logs to stdout/stderr.
# Ensure PHP errors are displayed (for development env) and logged.
# You might want to make display_errors 'Off' in production.
RUN echo "display_errors = On" > /usr/local/etc/php/conf.d/docker-php-display-errors.ini \
    && echo "error_reporting = E_ALL" >> /usr/local/etc/php/conf.d/docker-php-display-errors.ini \
    && echo "log_errors = On" >> /usr/local/etc/php/conf.d/docker-php-display-errors.ini \
    && echo "error_log = /dev/stderr" >> /usr/local/etc/php/conf.d/docker-php-display-errors.ini

# Generate CustomConfig from runtime environment variables
# CodeIgniter can directly read from $_ENV or getenv().
# This is the correct way for Railway where you set ENV vars in their dashboard.
RUN rm -f app/Config/CustomConfig.php # Remove the build-time generated file if it exists
COPY app/Config/CustomConfig.php.template app/Config/CustomConfig.php

# Berikan permission yang sesuai
RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html

# Expose port 80
EXPOSE 80

# Command to run Apache in the foreground
CMD ["apache2-foreground"]