# Gunakan image PHP dengan Apache
FROM php:8.2-apache

# Install dependensi sistem dan ekstensi PHP
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libzip-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    zip \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install intl pdo pdo_mysql gd zip

# Aktifkan mod_rewrite Apache
RUN a2enmod rewrite

# Ubah DocumentRoot ke folder `public` (CodeIgniter 4)
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|' /etc/apache2/sites-available/000-default.conf

# Izinkan penggunaan .htaccess
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Salin semua source code ke dalam container
COPY . /var/www/html

# Set working directory
WORKDIR /var/www/html

# Install dependencies via Composer
RUN composer install --no-interaction --no-dev --optimize-autoloader

# Berikan permission yang sesuai
RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html
