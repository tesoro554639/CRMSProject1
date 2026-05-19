FROM php:8.4-apache
# Install system packages and PHP extensions
RUN apt-get update && apt-get install -y \
 git \
 unzip \
 curl \
 libpq-dev \
 libzip-dev \
 libonig-dev \
 libxml2-dev \
 libpng-dev \
 zip \
 && docker-php-ext-install pdo pdo_mysql pdo_pgsql zip mbstring xml \
 && apt-get clean \
 && rm -rf /var/lib/apt/lists/*
# Enable Apache rewrite
RUN a2enmod rewrite
# Make Apache use Render's default web port 10000
RUN sed -i 's/Listen 80/Listen 10000/g' /etc/apache2/ports.conf \
&& sed -i 's/<VirtualHost \*:80>/<VirtualHost *:10000>/g'
/etc/apache2/sites-available/000-default.conf
# Set Laravel public as document root
RUN sed -i 's|/var/www/html|/var/www/html/public|g'
/etc/apache2/sites-available/000-default.conf \
&& sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/apache2.conf
# Allow .htaccess for Laravel
RUN printf '<Directory /var/www/html/public>\n\
 AllowOverride All\n\
 Require all granted\n\
</Directory>\n' > /etc/apache2/conf-available/laravel.conf \
&& a2enconf laravel
# Install Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
&& apt-get install -y nodejs
# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /var/www/html
# Copy full Laravel app
COPY . .
# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction
# Install frontend dependencies and build Vite assets
RUN npm install
RUN npm run build
# Create storage symlink for public files
RUN php artisan storage:link || true
# Set permissions
RUN mkdir -p storage/framework/cache storage/framework/sessions
storage/framework/views bootstrap/cache public/uploads \
&& chown -R www-data:www-data storage bootstrap/cache public/uploads \
&& chmod -R 775 storage bootstrap/cache public/uploads
EXPOSE 10000
CMD ["apache2-foreground"]