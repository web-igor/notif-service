FROM php:8.5-fpm

# Установка системных зависимостей
RUN apt-get update && apt-get install -y \
    unzip \
    libzip-dev \
    zip \
    libicu-dev \
    libpng-dev \
    libjpeg-dev \
    libwebp-dev \
    libfreetype6-dev \
    libavif-dev \
    libaom-dev \
    libxpm-dev \
    libjpeg62-turbo-dev \
    libpq-dev \
    libmagickwand-dev \
    libxml2-dev \
    postgresql-client-17 \
    cron \
    ffmpeg

# Очистка кэша apt
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Установка PHP расширений
RUN docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    mysqli \
    bcmath \
    sockets \
    calendar \
    gettext \
    pcntl \
    zip \
    simplexml \
    && docker-php-ext-configure gd --with-freetype --with-webp --with-jpeg --with-avif --with-xpm \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-configure intl && docker-php-ext-install intl \
    && docker-php-ext-configure exif && docker-php-ext-install exif \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo pdo_pgsql pgsql

# Установка Redis
RUN pecl install redis \
    && docker-php-ext-enable redis

# Копирование конфигурационных файлов
COPY ./docker/php/php.ini /usr/local/etc/php/conf.d/php.ini

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Настройка пользователя (usermod и groupmod уже установлены)
ARG UID=1000
ARG GID=1000
RUN usermod -u ${UID} www-data && groupmod -g ${GID} www-data

# Копируем entrypoint
COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

WORKDIR /var/www/html
