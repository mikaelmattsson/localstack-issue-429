FROM php:7.1

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php
RUN mv composer.phar /usr/local/bin/composer

# Install Git
RUN apt-get update
RUN apt-get install -y git

# Logs

RUN mkdir /logs
VOLUME ["/logs"]

RUN { \
        echo 'log_errors = On'; \
        echo 'error_log = /logs'; \
    } > /usr/local/etc/php/php.ini
