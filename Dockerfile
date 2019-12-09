FROM registry.gitlab.com/rollun/service-test-frontent/php-fpm:7.2-fpm-alpine
MAINTAINER rollun lc <it.professor02@gmail.com>

RUN docker-php-ext-install sockets

COPY composer.json /var/www/application/
COPY composer.lock /var/www/application/
RUN cd /var/www/application && composer install

ADD bin /var/www/application/bin
ADD config /var/www/application/config
ADD data /var/www/application/data
ADD public /var/www/application/public
ADD src /var/www/application/src
COPY LICENSE.md /var/www/application/
COPY phpcs.xml /var/www/application/
COPY README.md /var/www/application/

WORKDIR /var/www/application/public


CMD ["php-fpm"]
