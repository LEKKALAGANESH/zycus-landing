# syntax=docker/dockerfile:1
FROM php:8.2-apache

# ------------------------------------------------------------
# System deps + PHP extensions
# ------------------------------------------------------------
RUN apt-get update \
 && apt-get install -y --no-install-recommends \
      libzip-dev \
      unzip \
      git \
 && docker-php-ext-install pdo pdo_mysql zip \
 && a2enmod rewrite headers expires deflate \
 && rm -rf /var/lib/apt/lists/*

# Composer from the official image
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ------------------------------------------------------------
# Apache: serve from public/, allow .htaccess overrides
# ------------------------------------------------------------
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
        /etc/apache2/sites-available/*.conf \
        /etc/apache2/apache2.conf \
        /etc/apache2/conf-available/*.conf \
 && sed -ri -e 's!AllowOverride None!AllowOverride All!g' /etc/apache2/apache2.conf

# ------------------------------------------------------------
# App
# ------------------------------------------------------------
WORKDIR /var/www/html
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts --prefer-dist

COPY . .

# Ensure runtime dirs are writable
RUN mkdir -p storage/logs \
 && chown -R www-data:www-data storage \
 && chmod -R 775 storage

# ------------------------------------------------------------
# Render/Fly/other PaaS-friendly: listen on $PORT (default 10000)
# Apache config references the `${PORT}` env var, evaluated by
# mod_env at startup. The ENV line provides a fallback for local.
# ------------------------------------------------------------
ENV PORT=10000
RUN sed -ri -e 's!^Listen 80$!Listen ${PORT}!' /etc/apache2/ports.conf \
 && sed -ri -e 's!<VirtualHost \*:80>!<VirtualHost *:${PORT}>!' /etc/apache2/sites-available/000-default.conf

EXPOSE 10000

CMD ["apache2-foreground"]
