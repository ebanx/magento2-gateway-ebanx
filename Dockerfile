FROM php:7-apache

EXPOSE 80

# install the PHP extensions we need
RUN set -ex; \
    \
    apt-get update; \
    apt-get install -y --no-install-recommends \
      libjpeg-dev \
      libpng-dev \
      libpng-dev \
      libicu-dev \
      libxml2-dev \
      libxslt-dev \
      libzip-dev \
      libfreetype6-dev \
      mariadb-client \
      zip \
      unzip \
    ; \
    rm -rf /var/lib/apt/lists/*; \
    \
    docker-php-ext-configure gd --with-jpeg --with-freetype; \
    docker-php-ext-configure intl; \
    docker-php-ext-install gd opcache pdo_mysql bcmath intl soap xsl zip sockets

RUN { \
		echo 'opcache.memory_consumption=128'; \
		echo 'opcache.interned_strings_buffer=8'; \
		echo 'opcache.max_accelerated_files=4000'; \
		echo 'opcache.revalidate_freq=2'; \
		echo 'opcache.fast_shutdown=1'; \
		echo 'opcache.enable_cli=1'; \
	} > /usr/local/etc/php/conf.d/opcache-recommended.ini

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"; \
    php -r "if (hash_file('sha384', 'composer-setup.php') === '906a84df04cea2aa72f40b5f787e49f22d4c2f19492ac310e8cba5b96ac8b64115ac402c8cd292b8a03482574915d1a8') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"; \
    php composer-setup.php; \
    php -r "unlink('composer-setup.php');"; \
    mv composer.phar /bin/composer; \
    chmod +x /bin/composer

RUN a2enmod rewrite expires
RUN sed -i 's/\/var\/www\/html/\/var\/www\/html\/pub/g' /etc/apache2/sites-available/000-default.conf

COPY wait-for-it.sh /usr/local/bin/
COPY entrypoint.sh /usr/local/bin/

RUN chmod +x /usr/local/bin/wait-for-it.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# just to make sure that my host's user is the same as the container's user
# because we will need some write permissions
RUN usermod -u 1000 www-data
RUN groupmod -g 1000 www-data

# we need tha username and password to install Magento 2 via composer
RUN echo "{\"http-basic\": {\"repo.magento.com\": {\"username\": \"$MAGENTO_REPO_USER\", \"password\": \"$MAGENTO_REPO_PASSWORD\"}}}" > ~/.composer/auth.json
RUN composer create-project --repository-url=https://repo.magento.com/ magento/project-community-edition=2.4.2 /var/www/html
RUN composer require ebanx/benjamin

WORKDIR /var/www/html
ENTRYPOINT /usr/local/bin/entrypoint.sh
