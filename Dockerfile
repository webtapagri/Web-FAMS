FROM php:7.2-apache

USER root

WORKDIR /var/www/html/Web-FAMS
RUN	apt-get update && apt-get -y install vim cron curl libaio1
RUN apt-get update && apt-get install libldap2-dev -y \
 && rm -rf /var/lib/apt/lists/* \
 && docker-php-ext-configure ldap --with-libdir=lib/x86_64-linux-gnu/ \
 && docker-php-ext-install ldap


ARG ENV
ENV envValue=$ENV

COPY DockerAsset/entry.sh /entry.sh
RUN chmod 755  /entry.sh
RUN chmod 777  /etc

COPY DockerAsset/vhost.conf.${envValue} /etc/apache2/sites-available/000-default.conf
COPY DockerAsset/servername.conf /etc/apache2/conf-available/servername.conf
COPY DockerAsset/etc.resolv.conf /etc/resolv.conf

COPY . .

RUN chown -R www-data:www-data /var/www/html/Web-FAMS \
    && a2enmod rewrite \
	&& chmod -R 777 /var/www/html/Web-FAMS

RUN rm /etc/apt/preferences.d/no-debian-php  \
    && apt-get update && apt-get install -y libxml2-dev php-soap  \
    && docker-php-ext-install soap \
    && docker-php-ext-install intl \
    && docker-php-ext-install mysqli pdo pdo_mysql \
    && docker-php-ext-enable pdo_mysql \
    && apt-get install -y libpng-dev && docker-php-ext-install gd

RUN apt-get install -y \
    libzip-dev \
    zip \
    && docker-php-ext-configure zip --with-libzip \
    && docker-php-ext-install zip

RUN a2enconf servername

RUN curl -sS https://getcomposer.org/installer -o composer-setup.php \
	&& HASH=`curl -sS https://composer.github.io/installer.sig` \
	&& php -r "if (hash_file('SHA384', 'composer-setup.php') === '$HASH') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"

RUN php composer-setup.php --install-dir=/usr/local/bin --filename=composer

COPY --chown=www-data:www-data DockerAsset/.env.${envValue} /var/www/html/Web-FAMS/.env

COPY DockerAsset/php.ini.dev /usr/local/etc/php/php.ini
RUN chmod -R 777 storage
RUN chmod -R 777 public

CMD ["/entry.sh"]
	
EXPOSE 80
