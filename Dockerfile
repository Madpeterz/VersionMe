FROM madpeter/phpapachepreload:latest

MAINTAINER Madpeter

ENV SALTCODE_SERVER=''
ENV SALTCODE_USER=''

COPY --chown=www-data:www-data . /srv/website
COPY .docker/vhost.conf /etc/apache2/sites-available/000-default.conf

WORKDIR /srv/website

RUN apt-get update && composer install --no-dev
