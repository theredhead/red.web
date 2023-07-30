FROM php:7.4-apache

RUN apt-get update

RUN apt-get install --yes --force-yes cron g++ gettext libicu-dev openssl libc-client-dev libkrb5-dev libxml2-dev libfreetype6-dev libgd-dev libmcrypt-dev bzip2 libbz2-dev libtidy-dev libcurl4-openssl-dev libz-dev libmemcached-dev libxslt-dev

RUN docker-php-ext-configure gd --with-freetype=/usr --with-jpeg=/usr
RUN docker-php-ext-install gd

RUN a2enmod rewrite

COPY tools/000-default.conf /etc/apache2/sites-available/000-default.conf
# Copy application source
COPY build/app /var/www/
RUN chown -R www-data:www-data /var/www

CMD ["apache2-foreground"]
