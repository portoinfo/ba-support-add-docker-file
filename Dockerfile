# Stage 1: PHP
FROM baimages/php-nginx:7.4-alpine as php

# Install necessary PHP extensions and dependencies
RUN apk add --update --no-cache shadow \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    icu-dev \
    && docker-php-ext-install zip gd pcntl intl

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set the working directory for this stage
WORKDIR /var/www

# Copy Laravel project files to the PHP stage
COPY . .

# Create user  www-data
RUN useradd -G www-data,root -u 1000 -d /home/dev dev
RUN mkdir -p /home/dev/.composer && \
    chown -R dev:dev /home/dev

### Fix issue of missing php7.4 creating a syslink 
##RUN ln -s /usr/local/bin/php /usr/local/bin/php7.4

# Install project dependencies
RUN composer config --global --auth http-basic.builderall.repo.repman.io token b8b9f8a8a330a945f64576b53dc9b3884e005069f71175443950304691d0e4f0

# Install Composer dependencies and optimize autoloader
RUN composer install --no-dev --no-interaction --optimize-autoloader \
    && composer dump-autoload

# Stage 2: Node.js
##FROM node:16-alpine as node

# Set the working directory for this stage
##WORKDIR /var/www

# Copy Laravel project files from the PHP stage to the Node.js stage
##COPY --from=php /var/www .

# Install Node.js dependencies and compile JavaScript assets
RUN npm ci && npm run prod

# Clean up unnecessary files
RUN rm -rf /var/www/node_modules/

# Set the working directory for this stage
WORKDIR /var/www

# Copy Laravel project files
COPY --chown=www-data:www-data  --from=node  /var/www   /var/www

# Set the NGINX root directory environment variable
ENV NGINX_ROOT=/var/www/public

EXPOSE 80
