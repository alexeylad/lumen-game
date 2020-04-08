FROM php:7.4-fpm-alpine

# Copy composer.lock and composer.json
COPY composer.lock composer.json /var/www/

# Set working directory
WORKDIR /var/www

# Install dependencies
RUN apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
        curl-dev \
        imagemagick-dev \
        freetype-dev \
        libpng-dev \
        libjpeg-turbo-dev \
        libtool \
        libxml2-dev \
    && apk add --no-cache \
        shadow \
        curl \
        git \
        imagemagick \
        mysql-client \
        libintl \
        icu \
        icu-dev \
        libzip-dev \
    && pecl install xdebug \
    && echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" > /usr/local/etc/php/conf.d/xdebug.ini \
    && pecl install imagick redis \
    && docker-php-ext-enable imagick redis \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        curl \
        gd \
        pdo_mysql \
        pcntl \
        bcmath \
        zip \
        intl \
    && curl -s https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin/ --filename=composer \
    && apk del -f .build-deps

# Add user for laravel application
RUN groupadd -g 1000 www
RUN useradd -u 1000 -ms /bin/bash -g www www

# Copy existing application directory contents
COPY . /var/www

# Copy existing application directory permissions
COPY --chown=www:www . /var/www

# Change current user to www
USER www

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]
