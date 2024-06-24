FROM php:8.1.0-apache

#php setup, install extensions, setup configs
RUN apt-get update && apt-get install --no-install-recommends -y \
    libzip-dev \
    libxml2-dev \
    mariadb-client \
    zip \
    unzip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*
    
RUN apt-get install -y zlib1g-dev libicu-dev g++ \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl

    
RUN pecl install zip pcov
RUN docker-php-ext-enable zip \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-install bcmath \
    && docker-php-ext-install soap \
    && docker-php-source delete



#disable exposing server information
RUN sed -ri -e 's!expose_php = On!expose_php = Off!g' $PHP_INI_DIR/php.ini-production \
    && sed -ri -e 's!ServerTokens OS!ServerTokens Prod!g' /etc/apache2/conf-available/security.conf \
    && sed -ri -e 's!ServerSignature On!ServerSignature Off!g' /etc/apache2/conf-available/security.conf \
    && sed -ri -e 's!KeepAliveTimeout .*!KeepAliveTimeout 65!g' /etc/apache2/apache2.conf \
    && mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"


RUN a2enmod rewrite


#composer install
COPY --from=composer:2.1.9 /usr/bin/composer /usr/bin/composer




#setup task, for running Taskfiles
RUN curl -o /tmp/taskfile.tar.gz 'https://oberd-static-media.s3.amazonaws.com/builddeps/task/3.34.1/task_linux_386.tar.gz' \
    && tar -xzf /tmp/taskfile.tar.gz -C /tmp \
    && mv /tmp/task /usr/local/bin/task \
    && chmod +x /usr/local/bin/task


RUN apt-get install -y tzdata
ENV TZ America/Lima
RUN ln -snf /usr/share/zoneinfo/${TZ} /etc/localtime
RUN echo "${TZ}" > /etc/timezone
COPY . /var/www/html/
RUN chmod -R a+r /var/www/html

RUN chmod -R 755 writable/ 
RUN chown -R www-data:www-data writable/

