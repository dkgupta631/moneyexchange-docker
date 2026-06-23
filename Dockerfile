# Stage 1: Frontend build
FROM node:22-alpine AS frontend
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
RUN npm run build

# Stage 2: PHP dependencies
# --ignore-platform-reqs: composer:2 bundles its own PHP (currently 8.5) which
# differs from the runtime (php:8.2). Extensions and version constraints are
# satisfied by the runtime image in Stage 3.
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install \
    --no-interaction \
    --no-dev \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts \
    --ignore-platform-reqs

# Stage 3: PHP app
FROM php:8.2-fpm-alpine AS app
WORKDIR /var/www/html

RUN apk add --no-cache \
    bash curl git unzip zip supervisor \
    freetype-dev jpeg-dev libjpeg-turbo-dev libpng-dev libwebp-dev \
    libxml2-dev libzip-dev icu-dev oniguruma-dev mysql-client \
 && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
 && docker-php-ext-install -j$(nproc) \
    bcmath exif gd intl mbstring pdo pdo_mysql pcntl xml zip

# Disable SSL for mysql client (server runs with --skip-ssl)
RUN printf "[client]\nssl=FALSE\n" > /root/.my.cnf

COPY --from=vendor /app/vendor ./vendor
COPY --from=frontend /app/public/build ./public/build
COPY . .

RUN rm -f public/hot \
 && cp -r ./public /public_init \
 && chown -R www-data:www-data storage bootstrap/cache \
 && chmod -R 775 storage bootstrap/cache

COPY docker/supervisor.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 9000
ENTRYPOINT ["/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
