FROM php:8.1-apache

# Install dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy composer files first
COPY composer.json ./

# Remove composer.lock and vendor directory if they exist
RUN rm -f composer.lock && rm -rf vendor/

# Install dependencies
RUN composer install --no-interaction --no-dev --optimize-autoloader

# Copy the rest of the application
COPY . .

# Change ownership of our applications
RUN chown -R www-data:www-data /var/www/html

# Configure Apache
RUN a2enmod rewrite
COPY docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf

# Set environment variables
ENV CI_ENVIRONMENT=production
ENV app.baseURL=${APP_URL}
ENV database.default.hostname=${DB_HOST}
ENV database.default.database=${DB_NAME}
ENV database.default.username=${DB_USER}
ENV database.default.password=${DB_PASSWORD}
ENV database.default.DBDriver=MySQLi
ENV database.default.port=${DB_PORT}

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"] 