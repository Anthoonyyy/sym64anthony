# Image de base PHP-FPM Alpine optimisée
FROM php:8.1-fpm-alpine

# Installations des dépendances système
RUN apk add --no-cache \
    git \
    unzip \
    libpng-dev \
    libzip-dev \
    postgresql-dev \
    && docker-php-ext-install \
    pdo_mysql \
    pdo_pgsql \
    gd \
    zip \
    intl


COPY --from=composer:2.5 /usr/bin/composer /usr/bin/composer

# Configuration du répertoire de travail
WORKDIR /var/www/symfony

# Optimisation des dépendances Composer
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader

# Copie du reste des sources
COPY . .

# Génération de l'autoloader Composer
RUN composer dump-autoload --no-dev --optimize

# Configuration PHP-FPM
COPY php-fpm.conf /usr/local/etc/php-fpm.d/custom.conf

# Permissions
RUN chown -R www-data:www-data /var/www/symfony

# Utilisateur non-root
USER www-data

# Point d'entrée
CMD ["php-fpm"]