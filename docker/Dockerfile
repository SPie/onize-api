FROM php:8.2-apache

COPY . /var/www/html
COPY docker/vhost.conf /etc/apache2/sites-available/000-default.conf

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_host = 192.168.188.23" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.idekey = onize-api-docker" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.log=/dev/stdout" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.log_level=0" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

RUN apt-get update -y && apt-get install -y libpq-dev

RUN chown -R www-data:www-data /var/www/html \
    && docker-php-ext-install pdo pdo_pgsql \
    && a2enmod rewrite
