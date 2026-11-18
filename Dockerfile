FROM php:8.3-apache

ENV DEBIAN_FRONTEND=noninteractive

# Install system dependencies and PHP extensions required by Laravel 13, Filament 3, DomPDF, and ZipArchive
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libwebp-dev \
    libfreetype6-dev \
    libicu-dev \
    libxml2-dev \
    libonig-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        mbstring \
        zip \
        gd \
        intl \
        bcmath \
        exif \
        opcache \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Enable single MPM (mpm_prefork for mod_php), Apache modules, and point DocumentRoot to /var/www/html/public
RUN a2dismod -f mpm_event mpm_worker || true \
    && rm -f /etc/apache2/mods-enabled/mpm_event.* /etc/apache2/mods-enabled/mpm_worker.* \
    && a2enmod mpm_prefork rewrite expires headers \
    && sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!/var/www/html/public!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf \
    && sed -ri -e 's!AllowOverride None!AllowOverride All!g' /etc/apache2/apache2.conf \
    && echo "ServerName localhost" > /etc/apache2/conf-available/servername.conf \
    && a2enconf servername \
    && apache2ctl -t

# PHP runtime and OPcache tuning
RUN { \
        echo 'memory_limit=256M'; \
        echo 'upload_max_filesize=20M'; \
        echo 'post_max_size=25M'; \
        echo 'opcache.enable=1'; \
        echo 'opcache.memory_consumption=128'; \
        echo 'opcache.interned_strings_buffer=16'; \
        echo 'opcache.max_accelerated_files=10000'; \
        echo 'opcache.revalidate_freq=30'; \
    } > /usr/local/etc/php/conf.d/evitron.ini

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --no-scripts --optimize-autoloader

COPY . .

RUN composer dump-autoload --optimize --no-interaction \
    && php artisan package:discover --ansi \
    && mkdir -p \
        storage/app/private/surat_peminjaman \
        storage/app/public \
        storage/framework/cache/data \
        storage/framework/cache/locks \
        storage/framework/sessions \
        storage/framework/testing \
        storage/framework/views \
        storage/logs \
        bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

# Create Linux-native startup script inside the image (no host CRLF issues)
RUN { \
        echo '#!/bin/sh'; \
        echo 'set -e'; \
        echo 'PORT_NUM="${PORT:-8080}"'; \
        echo 'sed -ri "s/^Listen [0-9]+/Listen ${PORT_NUM}/" /etc/apache2/ports.conf'; \
        echo 'sed -ri "s/<VirtualHost \*:[0-9]+>/<VirtualHost *:${PORT_NUM}>/" /etc/apache2/sites-available/000-default.conf'; \
        echo 'mkdir -p storage/app/private/surat_peminjaman storage/app/public storage/framework/cache/data storage/framework/cache/locks storage/framework/sessions storage/framework/views storage/logs bootstrap/cache'; \
        echo 'rm -f public/storage'; \
        echo 'php artisan storage:link || true'; \
        echo 'MIGRATED=0'; \
        echo 'for i in 1 2 3 4 5 6 7 8 9 10 11 12 13 14 15; do'; \
        echo '  if php artisan migrate --force; then'; \
        echo '    MIGRATED=1'; \
        echo '    break'; \
        echo '  fi'; \
        echo '  echo "Waiting for database connection (${i}/15)..."'; \
        echo '  sleep 2'; \
        echo 'done'; \
        echo 'if [ "$MIGRATED" -ne 1 ]; then'; \
        echo '  echo "Database migration failed after retries." >&2'; \
        echo '  exit 1'; \
        echo 'fi'; \
        echo 'php artisan db:seed --class=SettingSeeder --force'; \
        echo 'php artisan db:seed --class=VideotronSeeder --force'; \
        echo 'if [ -n "$DEMO_ADMIN_EMAIL" ] && [ -n "$DEMO_ADMIN_PASSWORD" ]; then'; \
        echo '  php -r '\''require "vendor/autoload.php"; $app = require "bootstrap/app.php"; $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap(); App\Models\User::updateOrCreate(["email" => getenv("DEMO_ADMIN_EMAIL")], ["name" => "Admin PUSTIKOM E-VITRON", "password" => Illuminate\Support\Facades\Hash::make(getenv("DEMO_ADMIN_PASSWORD")), "role" => "admin", "identifier" => "ADM-001", "phone" => "081234567890", "unit_or_prodi" => "PUSTIKOM UINSSC", "auth_type" => "local", "account_status" => "approved"]);'\'''; \
        echo 'fi'; \
        echo 'php artisan optimize:clear'; \
        echo 'php artisan optimize'; \
        echo 'php artisan filament:optimize'; \
        echo 'chown -R www-data:www-data storage bootstrap/cache'; \
        echo 'rm -f /etc/apache2/mods-enabled/mpm_event.* /etc/apache2/mods-enabled/mpm_worker.*'; \
        echo 'exec apache2-foreground'; \
    } > /usr/local/bin/start-evitron.sh && chmod +x /usr/local/bin/start-evitron.sh

EXPOSE 8080

CMD ["/usr/local/bin/start-evitron.sh"]
