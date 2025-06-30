# Gunakan image FrankenPHP sebagai basis
FROM dunglas/frankenphp:php8.3

ENV SERVER_NAME=":80"

# Atur working directory di dalam container
WORKDIR /app

# Install dependensi sistem dan ekstensi PHP.
# Pastikan setiap baris diakhiri dengan '\' tanpa ada spasi sesudahnya.
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) intl gd zip pdo_mysql \ 
    && docker-php-ext-enable intl gd zip pdo_mysql \ 
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Copy kode aplikasi Laravel-mu ke dalam container
COPY . /app

# Copy composer dari image resminya
COPY --from=composer:2.2 /usr/bin/composer /usr/bin/composer

# Jalankan composer install
RUN composer install --no-interaction --optimize-autoloader --no-dev

# Set permission agar storage dan bootstrap/cache bisa ditulis oleh server
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache

# Expose port yang digunakan oleh FrankenPHP
EXPOSE 80
EXPOSE 443
EXPOSE 443/udp

# Set Caddyfile (konfigurasi server)
COPY Caddyfile /etc/caddy/Caddyfile