# Gunakan image PHP dengan Apache
FROM php:8.2-apache

# Install ekstensi PHP yang dibutuhkan termasuk intl
RUN apt-get update && apt-get install -y \
    libicu-dev \
    zip \
    unzip \
    && docker-php-ext-install intl pdo pdo_mysql

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Salin semua source code ke dalam container
COPY . /var/www/html

# Set working directory
WORKDIR /var/www/html

# Install dependencies via Composer
RUN composer install --no-interaction --no-dev --optimize-autoloader

# Berikan permission yang sesuai
RUN chown -R www-data:www-data /var/www/html

# Aktifkan Apache mod_rewrite (dibutuhkan oleh CodeIgniter)
RUN a2enmod rewrite
