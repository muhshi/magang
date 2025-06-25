# Gunakan image FrankenPHP sebagai basis
FROM dunglas/frankenphp

# Atur working directory di dalam container
WORKDIR /app

# Copy kode aplikasi Laravel-mu ke dalam container
COPY . /app

# Pastikan Caddyfile disalin ke lokasi yang diharapkan oleh FrankenPHP/Caddy
COPY Caddyfile /etc/frankenphp/Caddyfile

# Instal dependensi sistem yang dibutuhkan untuk ekstensi PHP
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libwebp-dev \
    libzip-dev \
    libxml2-dev \
    libonig-dev \
    && rm -rf /var/lib/apt/lists/*

# Instal ekstensi PHP yang umum dibutuhkan Laravel/Filament
RUN docker-php-ext-install \
    pdo_mysql \
    gd \
    zip \
    mbstring \
    xml \
    opcache \
    exif \
    intl

# Instal Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Instal dependensi Composer
RUN composer install --no-dev --optimize-autoloader

# Clear Laravel's configuration cache BEFORE optimizing, to ensure env vars are read
RUN php artisan config:clear

# Buat cache aplikasi Laravel (penting untuk performa)
RUN php artisan optimize

# Pastikan hak akses file dan folder untuk Laravel
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# EXPOSE ports are already handled by the base image, no need to duplicate
# (Tidak ada baris EXPOSE 80 dan EXPOSE 443 di sini karena sudah dihapus)