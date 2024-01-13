ARG PHP_VERSION

FROM ${PHP_VERSION}

ARG TZ
ARG PROJECT_NAME
ENV APP_ENV=dev

WORKDIR /var/www

RUN apt-get update

# Install extensions
RUN apt-get install -y acl \
    libfcgi-dev \
    libicu-dev \
    libzip-dev \
    libpng-dev \
    file \
    gettext \
    git \
    libfontconfig1 \
    libxrender1 \
    wget

RUN pecl install apcu xdebug timezonedb
RUN docker-php-ext-enable apcu xdebug timezonedb
COPY ./xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

RUN docker-php-ext-install \
    gd \
    intl \
    opcache \
    zip

RUN docker-php-ext-install mysqli pdo pdo_mysql && docker-php-ext-enable pdo_mysql

# Install composer
COPY --from=composer:2.2.22 /usr/bin/composer /usr/local/bin/composer

COPY ./app.ini /usr/local/etc/php/conf.d/app.ini

# Set Timezone
RUN ln -snf /usr/share/zoneinfo/${TZ} /etc/localtime && echo ${TZ} > /etc/timezone

# Set current work dir
WORKDIR /var/www/$PROJECT_NAME

CMD [ "php-fpm" ]
