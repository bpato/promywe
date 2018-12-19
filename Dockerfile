FROM php:7-apache
# modulos utilizados principalmente por phpmyadmin
RUN docker-php-ext-install mysqli pdo_mysql; a2enmod rewrite && apachectl restart
