FROM php:7-apache
RUN docker-php-ext-install mysqli pdo_mysql; a2enmod rewrite && apachectl restart
