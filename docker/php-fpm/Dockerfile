#
#--------------------------------------------------------------------------
# Image Setup
#--------------------------------------------------------------------------
#

ARG ARG_PHP_VERSION=7.3

FROM php:${ARG_PHP_VERSION}-fpm

#
#--------------------------------------------------------------------------
# Software's Installation
#--------------------------------------------------------------------------
#
# Installing tools and PHP extentions using "apt", "docker-php", "pecl",
#

# Install "curl", "libmemcached-dev", "libpq-dev", "libjpeg-dev",
#         "libpng-dev", "libfreetype6-dev", "libssl-dev", "libmcrypt-dev",
RUN apt-get update && \
  apt-get upgrade -y && \
  apt-get install -y --no-install-recommends \
    curl \
    libmemcached-dev \
    libz-dev \
    libpq-dev \
    libjpeg-dev \
    libpng-dev \
    libfreetype6-dev \
    libssl-dev \
    libmcrypt-dev \
  && rm -rf /var/lib/apt/lists/*



  # Install the PHP pdo_mysql extention
  RUN docker-php-ext-install pdo_mysql \
  # Install the PHP pdo_pgsql extention
  && docker-php-ext-install pdo_pgsql

# Set Environment Variables
ENV DEBIAN_FRONTEND noninteractive
# Configure non-root user.
ARG PUID=1000
ENV PUID ${PUID}
ARG PGID=1000
ENV PGID ${PGID}

RUN groupmod -o -g ${PGID} www-data && \
    usermod -o -u ${PUID} -g www-data www-data

# always run apt update when start and after add new source list, then clean up at end.
RUN set -xe; \
    apt-get update -yqq && \
    pecl channel-update pecl.php.net && \
    apt-get install -yqq \
      apt-utils \
      #
      #--------------------------------------------------------------------------
      # Mandatory Software's Installation
      #--------------------------------------------------------------------------
      #
      # Mandatory Software's such as ("mcrypt", "pdo_mysql", "libssl-dev", ....)
      # are installed on the base image 'laradock/php-fpm' image. If you want
      # to add more Software's or remove existing one, you need to edit the
      # base image (https://github.com/Laradock/php-fpm).
      #
      # next lines are here becase there is no auto build on dockerhub see https://github.com/laradock/laradock/pull/1903#issuecomment-463142846
      libzip-dev zip unzip && \
      docker-php-ext-configure zip; \
      # Install the zip extension
      docker-php-ext-install zip && \
      php -m | grep -q 'zip'

USER root

COPY ./xhprof.ini /usr/local/etc/php/conf.d

###########################################################################
# Opcache:
###########################################################################
RUN docker-php-ext-install opcache

# Copy opcache configration
COPY ./opcache.ini /usr/local/etc/php/conf.d/opcache.ini

###########################################################################
# Mysqli Modifications:
###########################################################################
RUN docker-php-ext-install mysqli

###########################################################################
# bcmath:
###########################################################################
RUN docker-php-ext-install bcmath

###########################################################################
# Human Language and Character Encoding Support:
###########################################################################
RUN apt-get install -y zlib1g-dev libicu-dev g++ && \
        docker-php-ext-configure intl && \
        docker-php-ext-install intl

###########################################################################
# ImageMagick:
###########################################################################
RUN apt-get install -y libmagickwand-dev imagemagick && \
        pecl install imagick && \
        docker-php-ext-enable imagick

###########################################################################
# Image optimizers:
###########################################################################

RUN apt-get install -y jpegoptim optipng pngquant gifsicle

###########################################################################
# PHP REDIS EXTENSION
###########################################################################

ARG INSTALL_PHPREDIS=false

RUN if [ $(php -r "echo PHP_MAJOR_VERSION;") = "5" ]; then \
      pecl install -o -f redis-4.3.0; \
    else \
      pecl install -o -f redis; \
    fi \
    && rm -rf /tmp/pear \
    && docker-php-ext-enable redis

# install composer
ENV COMPOSER_ALLOW_SUPERUSER 1
RUN curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer

#--------------------------------------------------------------------------
# Final Touch
#--------------------------------------------------------------------------
#
COPY ./laravel.ini /usr/local/etc/php/conf.d
COPY ./xlaravel.pool.conf /usr/local/etc/php-fpm.d/

USER root

# Clean up
RUN apt-get clean && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* && \
    rm /var/log/lastlog /var/log/faillog

RUN apt-get update
#--------------------------------------------------------------------------
# Set Timezone
#--------------------------------------------------------------------------
ENV TZ Asia/Bangkok
RUN apt-get install -y tzdata \
    && cp /usr/share/zoneinfo/$TZ /etc/localtime \
    && echo $TZ > /etc/timezone

ADD ./startup.sh /opt/startup.sh

RUN apt-get install -y htop git

ARG ARG_PHP_VERSION=7.3

#Install the PHP gd library
RUN if [ ${ARG_PHP_VERSION} = "7.3" ]; then \
    docker-php-ext-configure gd \
        --with-jpeg-dir=/usr/lib \
        --with-freetype-dir=/usr/include/freetype2 && \
    docker-php-ext-install gd \
;fi

RUN if [ ${ARG_PHP_VERSION} = "7.4" ] || [ ${ARG_PHP_VERSION} = "8.0" ] ; then \
    docker-php-ext-configure gd --with-freetype --with-jpeg && docker-php-ext-install gd \
;fi

RUN apt install -y libxslt-dev
RUN docker-php-ext-install soap xsl sockets

USER www-data
WORKDIR /var/www

ENTRYPOINT ["/bin/bash"]
CMD ["/opt/startup.sh"]

EXPOSE 9000 9001
