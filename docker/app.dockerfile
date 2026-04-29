# Stage 1: build frontend pakai bun
FROM oven/bun:latest AS frontend

WORKDIR /var/www/app
COPY package.json bun.lockb* ./
RUN bun install --frozen-lockfile

# Copy source and build
COPY . .
RUN bun run build

# Stage 2: PHP
FROM php:8.4-fpm-alpine

# Install bun
RUN apk add --no-cache bash && \
    curl -fsSL https://bun.sh/install | bash && \
    mv /root/.bun/bin/bun /usr/local/bin/bun

RUN set -ex \
    && apk add --no-cache \
        postgresql-dev \
        git \
        curl \
        zlib-dev \
        freetype \
        libpng \
        libjpeg-turbo \
        freetype-dev \
        libpng-dev \
        libjpeg-turbo-dev \
        libwebp-dev \
        libzip-dev \
        zip \
    && docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
        --with-webp \
    && docker-php-ext-install \
        pdo \
        pdo_pgsql \
        pdo_mysql \
        mysqli \
        gd \
        zip \
        bcmath

# Install Composer
RUN curl -sS https://getcomposer.org/installer \
    | php -- --install-dir=/usr/local/bin --filename=composer

# Fix git ownership issue
RUN git config --global --add safe.directory /var/www/app

WORKDIR /var/www/app

# Copy app source
COPY . .

# Copy built assets from frontend stage
COPY --from=frontend /var/www/app/public/build /var/www/app/public/build

# Install PHP deps (recommended)
RUN composer install --no-dev --optimize-autoloader

# Copy vendor to a backup location so it survives volume mounts
RUN cp -a /var/www/app/vendor /var/www/app/vendor-backup

# Entrypoint script to restore vendor if empty (due to volume mount)
RUN echo '#!/bin/sh' > /usr/local/bin/docker-entrypoint.sh && \
    echo 'if [ ! -f /var/www/app/vendor/autoload.php ]; then' >> /usr/local/bin/docker-entrypoint.sh && \
    echo '  echo "Restoring vendor directory from backup..."' >> /usr/local/bin/docker-entrypoint.sh && \
    echo '  cp -a /var/www/app/vendor-backup/* /var/www/app/vendor/' >> /usr/local/bin/docker-entrypoint.sh && \
    echo '  cp -a /var/www/app/vendor-backup/.* /var/www/app/vendor/ 2>/dev/null || true' >> /usr/local/bin/docker-entrypoint.sh && \
    echo 'fi' >> /usr/local/bin/docker-entrypoint.sh && \
    echo 'exec "$@"' >> /usr/local/bin/docker-entrypoint.sh && \
    chmod +x /usr/local/bin/docker-entrypoint.sh

RUN chown -R www-data:www-data \
    public/ storage/ bootstrap/ vendor/

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["php-fpm"]