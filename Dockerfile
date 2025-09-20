FROM php:7.4-apache

# Install mysqli and pdo_mysql extensions
RUN docker-php-ext-install mysqli pdo_mysql
