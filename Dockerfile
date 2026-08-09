############################################################################################
# 2) PHP STAGE — Main Production Container
##############################################
FROM php:8.3-fpm AS php_app

# Set working directory
WORKDIR /var/www

# Install system dependencies (FREETYPE HARUS DIINSTALL DULU)
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libzip-dev \
    zip unzip git curl \
    libonig-dev \
    libxml2-dev \
    locales \
    jpegoptim optipng pngquant gifsicle \
    vim \
    default-mysql-client \
    && rm -rf /var/lib/apt/lists/*

# Configure dan install GD extension dengan FreeType
RUN docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd

# Install PHP extensions lainnya
RUN docker-php-ext-install -j$(nproc) \
    zip \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath

##############################################
# Install Composer from official image
##############################################
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

##############################################
# Copy source code
##############################################
COPY . /var/www

##############################################
# Create required directories before composer
##############################################
RUN mkdir -p /var/www/bootstrap/cache /var/www/storage/logs /var/www/storage/framework/cache /var/www/storage/framework/sessions /var/www/storage/framework/views

##############################################
# Composer install (Production)
##############################################
RUN composer install --no-dev --optimize-autoloader --no-interaction

##############################################
# Set correct permissions
##############################################
RUN chown -R www-data:www-data \
        /var/www/storage \
        /var/www/bootstrap/cache

##############################################
# FPM
##############################################
EXPOSE 9000
CMD ["php-fpm"]