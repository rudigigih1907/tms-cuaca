FROM php:8.3-fpm-alpine

# Instal dependensi sistem dan ekstensi PHP yang dibutuhkan Yii2
RUN apk add --no-cache \
    bash \
    git \
    curl \
    tzdata \
    libpng-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    freetype-dev \
    libzip-dev \
    zip \
    unzip \
    icu-dev \
    oniguruma-dev

# Konfigurasi dan instal ekstensi PHP (gd, pdo_mysql, intl, zip, mbstring)
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) \
    gd \
    pdo_mysql \
    intl \
    zip \
    mbstring

# Instal Composer secara global
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Tentukan working directory di dalam container
WORKDIR /var/www/html

# Salin source code aplikasi ke dalam container
COPY . .

# Atur izin akses untuk folder runtime dan assets agar bisa ditulis oleh web server
# RUN chown -R www-data:www-data /var/www/html
RUN mkdir -p \
    /var/www/html/runtime \
    /var/www/html/web/assets \
    /var/www/html/web/uploads \
    && chown -R www-data:www-data /var/www/html

EXPOSE 9000

CMD ["php-fpm"]
