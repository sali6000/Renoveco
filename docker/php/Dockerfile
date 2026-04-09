FROM dunglas/frankenphp

# Dépendances système
RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    zip \
    libicu-dev \
    libzip-dev \
    libmagickwand-dev \
    && rm -rf /var/lib/apt/lists/*

# Extensions PHP nécessaires
RUN install-php-extensions \
    pdo_mysql \
    intl \
    mbstring \
    zip \
    opcache \
    imagick

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Config PHP perso
COPY docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini

WORKDIR /app