FROM madpeter/phpapachepreload:latest

MAINTAINER Madpeter

ENV SALTCODE_SERVER=''
ENV SALTCODE_USER=''

COPY --chown=www-data:www-data . /srv/website
COPY .docker/vhost.conf /etc/apache2/sites-available/000-default.conf

WORKDIR /srv/website

RUN apt-get update \
    && apt-get install -y zlib1g-dev libzip-dev unzip \
    && docker-php-ext-install zip \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer install --no-dev
