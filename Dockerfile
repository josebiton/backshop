FROM php:8.1.0-apache

# Actualización e instalación de paquetes básicos
RUN apt-get update && apt-get install --no-install-recommends -y \
    libzip-dev \
    libxml2-dev \
    mariadb-client \
    zip \
    unzip \
    zlib1g-dev \
    libicu-dev \
    g++ \
    tzdata \
    curl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Configuración de PHP y extensiones
RUN docker-php-ext-configure intl \
    && docker-php-ext-install intl \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-install bcmath \
    && docker-php-ext-install soap \
    && docker-php-ext-install zip \
    && docker-php-ext-install mysqli

# Instalar extensiones de PECL
RUN pecl install pcov \
    && docker-php-ext-enable pcov

# Deshabilitar la exposición de información del servidor
RUN sed -ri -e 's!expose_php = On!expose_php = Off!g' $PHP_INI_DIR/php.ini-production \
    && sed -ri -e 's!ServerTokens OS!ServerTokens Prod!g' /etc/apache2/conf-available/security.conf \
    && sed -ri -e 's!ServerSignature On!ServerSignature Off!g' /etc/apache2/conf-available/security.conf \
    && sed -ri -e 's!KeepAliveTimeout .*!KeepAliveTimeout 65!g' /etc/apache2/apache2.conf \
    && mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Habilitar mod_rewrite en Apache
RUN a2enmod rewrite

# Instalar Composer
COPY --from=composer:2.1.9 /usr/bin/composer /usr/bin/composer

# Instalar Taskfile
RUN curl -o /tmp/taskfile.tar.gz 'https://oberd-static-media.s3.amazonaws.com/builddeps/task/3.34.1/task_linux_386.tar.gz' \
    && tar -xzf /tmp/taskfile.tar.gz -C /tmp \
    && mv /tmp/task /usr/local/bin/task \
    && chmod +x /usr/local/bin/task

# Configuración de zona horaria
ENV TZ=America/Lima
RUN ln -snf /usr/share/zoneinfo/${TZ} /etc/localtime && echo "${TZ}" > /etc/timezone

# Copiar los archivos de la aplicación
COPY . /var/www/html/
RUN chmod -R a+r /var/www/html \
    && chmod -R 755 writable/ \
    && chown -R www-data:www-data writable/
