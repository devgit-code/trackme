FROM dunglas/frankenphp

RUN apt-get update && apt-get -y --no-install-recommends install wget

# add additional extensions here:
RUN install-php-extensions pdo_mysql
RUN install-php-extensions gd
RUN install-php-extensions intl
RUN install-php-extensions zip
RUN install-php-extensions opcache
RUN install-php-extensions redis
RUN install-php-extensions imagick
