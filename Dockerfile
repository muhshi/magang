# Gunakan image FrankenPHP sebagai basis
FROM dunglas/frankenphp:php8.4

ENV SERVER_NAME=":80"

# Atur working directory di dalam container
WORKDIR /app

# Install dependensi sistem dan ekstensi PHP yang dibutuhkan Laravel & Filament
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) intl gd zip pdo_mysql pcntl exif \
    && docker-php-ext-enable intl gd zip pdo_mysql pcntl exif \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Copy composer dari image resminya
COPY --from=composer:2.2 /usr/bin/composer /usr/bin/composer

# Copy Caddyfile (konfigurasi server)
COPY Caddyfile /etc/caddy/Caddyfile

# Copy kode aplikasi Laravel-mu ke dalam container
COPY . /app

# Jalankan composer install
RUN composer install --no-interaction --optimize-autoloader --no-dev

# Set permission agar storage dan bootstrap/cache bisa ditulis oleh server
RUN mkdir -p /app/storage /app/bootstrap/cache \
    && chown -R www-data:www-data /app/storage /app/bootstrap/cache

# Expose port yang digunakan oleh FrankenPHP
EXPOSE 80
EXPOSE 443
EXPOSE 443/udp