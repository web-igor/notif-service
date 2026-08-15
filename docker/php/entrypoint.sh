#!/bin/bash

# Создаем папки, если их нет
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/storage/app/private/reports
mkdir -p /var/www/html/storage/framework/{views,cache,sessions}
mkdir -p /var/www/html/bootstrap/cache

# Устанавливаем права
chown -R www-data:www-data /var/www/html/storage
chown -R www-data:www-data /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/bootstrap/cache

# Запускаем PHP-FPM
exec php-fpm
