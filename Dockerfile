FROM php:8.3-apache

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    libzip-dev \
    libpng-dev \
    nodejs \
    unzip \
    git \
    curl \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        zip \
        gd \
        exif \
    && pecl install xdebug redis \
    && docker-php-ext-enable xdebug redis \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - && apt-get install -y nodejs

ENV APACHE_DOCUMENT_ROOT /var/www/html/public

RUN git config --global --add safe.directory /var/www/html \
    && sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf \
    && a2enmod rewrite

EXPOSE 80

CMD ["apache2-foreground"]
