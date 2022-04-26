FROM php:8.1-apache

COPY . /var/www/html
COPY docker/vhost.conf /etc/apache2/sites-available/000-default.conf

RUN apt-get update -y && apt-get install -y libpq-dev

RUN chown -R www-data:www-data /var/www/html \
    && docker-php-ext-install pdo pdo_pgsql \
    && a2enmod rewrite
