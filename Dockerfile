FROM php:8.2-apache

# On installe les dépendances nécessaires
RUN apt-get update && apt-get install -y \
    libpq-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo pdo_pgsql

# Installation de Composer
RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin \
    --filename=composer

# Active le module Rewrite d’Apache
RUN a2enmod rewrite

# On fait pointer le VirtualHost sur le dossier public/ de Symfony
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html