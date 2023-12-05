FROM php:8.3-alpine

RUN apk --no-cache add $PHPIZE_DEPS git unzip linux-headers

RUN mkdir -p /usr/src/php/ext/xdebug \
    && curl -fsSL https://xdebug.org/files/xdebug-3.3.0.tgz | tar xvz -C "/usr/src/php/ext/xdebug" --strip 1 \
    && docker-php-ext-install xdebug

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

CMD ["php", "-S", "0.0.0.0:8080", "-t", "/app", "/app/index.php"]