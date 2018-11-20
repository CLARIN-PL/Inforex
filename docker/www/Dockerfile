FROM php:5.6-apache

ENV inforex_location=/home/inforex

RUN apt-get update -y && apt-get upgrade -y
RUN apt-get install software-properties-common wget -y
RUN apt-get install vim -y

# Workaround for write permission on write to MacOS X volumes
# See https://github.com/boot2docker/boot2docker/pull/534
RUN usermod -u 1000 www-data

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Enable Apache mod_rewrite
RUN a2enmod headers

# Enable Apache mod_rewrite
RUN a2enmod expires

#Installing some PHP libraries
RUN apt-get install libmcrypt-dev zlib1g zlib1g-dev -y \
	&& docker-php-ext-install mcrypt \
	&& docker-php-ext-install mysql \
	&& docker-php-ext-install zip \
	&& apt-get install re2c -y

#Installing libxdiff library
RUN wget http://www.xmailserver.org/libxdiff-0.23.tar.gz \
	&& tar -xvf libxdiff-0.23.tar.gz \
	&& cd libxdiff-0.23 \
	&& ./configure \
	&& make \
	&& make install \
	&& ldconfig 

WORKDIR /var/www

#Setting up virtual host
RUN echo "  Alias /inforex $inforex_location/public_html\n  <Directory $inforex_location/public_html>\n    Require all granted\n  </Directory>\n" | tee /etc/apache2/sites-available/inforex.conf

#Set up symbolinc link
WORKDIR /etc/apache2/sites-enabled/
RUN ln -s ../sites-available/inforex.conf inforex.conf

COPY config.local.php $inforex_location/engine/config.local.php

COPY setup.sh /bin
RUN chmod -R +x /bin/setup.sh

CMD /bin/setup.sh \
    && apache2-foreground