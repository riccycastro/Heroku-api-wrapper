FROM php:7.4-fpm-alpine

RUN \
    apk add --no-cache curl bash $PHPIZE_DEPS

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN pecl install xdebug && docker-php-ext-enable xdebug

RUN echo "xdebug.remote_enable=on" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_autostart=off" >> /usr/local/etc/php/conf.d/xdebug.ini

RUN echo Europe/Portugal > /etc/timezone

COPY ./entrypoint.sh /

RUN ["chmod", "+x", "/entrypoint.sh"]

WORKDIR /var/www/html/internations-app

ENTRYPOINT ["/entrypoint.sh"]

EXPOSE 9000

CMD ["php-fpm"]
