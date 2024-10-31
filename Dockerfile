FROM php:8.1-fpm-alpine
 
# Installation des dépendances système
RUN apk add --no-cache \
    libzip-dev \
    icu-dev \
    git \
    zip \
    unzip
 
# Installation des extensions PHP
RUN docker-php-ext-install \
    pdo_pgsql \
    zip \
    intl
 
WORKDIR /usr/src/app
 
# Installation de Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
 
# Copie des fichiers de configuration
COPY composer.* ./
 
# Ajout des répertoires bin au PATH
ENV PATH="${PATH}:/usr/src/app/vendor/bin:bin"
 
# Copie du reste des fichiers
COPY . .
 
# Add this environment variable before running composer install
ENV COMPOSER_ALLOW_SUPERUSER=1
 
# Installation des dépendances
RUN composer install --optimize-autoloader
 
# Configuration des permissions
RUN chown -R www-data:www-data var
 
# Installation de MySQL
RUN apk add --no-cache mysql-client \
    && docker-php-ext-install pdo pdo_mysql