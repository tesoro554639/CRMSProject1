FROM php:8.4-apache

# Install system packages and PHP extensions
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    libpng-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    zip \
    && docker-php-ext-install pdo pdo_mysql zip mbstring xml \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Enable Apache rewrite
RUN a2enmod rewrite

# Use Render's default port 10000
RUN sed -i 's/Listen 80/Listen 10000/g' /etc/apache2/ports.conf \
    && sed -i 's/<VirtualHost \*:80>/<VirtualHost *:10000>/g' \
        /etc/apache2/sites-available/000-default.conf

# Set Laravel public as document root
RUN sed -i 's|/var/www/html|/var/www/html/public|g' \
        /etc/apache2/sites-available/000-default.conf \
    && sed -i 's|/var/www/html|/var/www/html/public|g' \
        /etc/apache2/apache2.conf

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

# Copy dependency files first (better layer caching)
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

COPY package.json package-lock.json ./
RUN npm ci

# Copy the full app
COPY . .

# Run post-install scripts now that full app is present
RUN composer run-script post-autoload-dump || true

# Build frontend assets
RUN npm run build

# Set permissions
RUN mkdir -p storage/framework/cache storage/framework/sessions \
        storage/framework/views bootstrap/cache public/uploads \
    && chown -R www-data:www-data storage bootstrap/cache public/uploads \
    && chmod -R 775 storage bootstrap/cache public/uploads

# Create startup script (storage:link runs at container start, not build time)
RUN printf '#!/bin/bash\nphp artisan storage:link --force 2>/dev/null || true\nexec apache2-foreground\n' \
    > /usr/local/bin/start.sh && chmod +x /usr/local/bin/start.sh

EXPOSE 10000

<<<<<<< HEAD
CMD ["/usr/local/bin/start.sh"]
=======
CMD ["/usr/local/bin/start.sh"]
>>>>>>> 25b3e72d1b2105edca3c1bb44816fac0420c5860
