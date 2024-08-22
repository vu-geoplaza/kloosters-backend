FROM php:8.3-apache

RUN apt-get update && docker-php-ext-install pdo pdo_mysql

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

COPY . /var/www/html/resources

RUN sed -i 's/<VirtualHost \*\:80>/<VirtualHost \*\:8080>/g' /etc/apache2/sites-enabled/000-default.conf && sed -i 's/Listen 80/Listen 8080/g' /etc/apache2/ports.conf