FROM php:8.2-fpm

# Install system dependencies
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
WORKDIR /var/www

# Copy application source
COPY . .

# Copy .env file (required for artisan commands)
COPY .env .  # Make sure .env exists in your project root

# Install dependencies without running scripts
RUN composer install --no-scripts

# Generate app key (requires .env)
RUN php artisan key:generate

# Run post-autoload-dump scripts manually
RUN composer run-script post-autoload-dump

# Set correct permissions
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
RUN chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Expose port 8000
EXPOSE 8000

# Start PHP built-in server
CMD php artisan serve --host=0.0.0.0 --port=8000
