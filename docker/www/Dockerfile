FROM php:5.6-apache

ENV inforex_location=/home/inforex

RUN apt-get update -y && apt-get upgrade -y
RUN apt-get install software-properties-common wget -y
RUN apt-get install vim p7zip-full -y

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

# Xdiff install
RUN curl -L -o /tmp/xdiff.tar.gz "http://www.xmailserver.org/libxdiff-0.23.tar.gz" \
    && mkdir -p /usr/src/xdiff \
    && tar -C /usr/src/xdiff -zxvf /tmp/xdiff.tar.gz --strip 1 \
    && rm /tmp/xdiff.tar.gz \
    && cd /usr/src/xdiff \
    && ./configure  && make && make install \
    \
    &&  pecl install xdiff-1.5.2 && docker-php-ext-enable xdiff

WORKDIR /var/www

#Setting up virtual host
RUN echo "  Alias /inforex $inforex_location/public_html\n  <Directory $inforex_location/public_html>\n    Require all granted\n  </Directory>\n" | tee /etc/apache2/sites-available/inforex.conf

#Set up symbolinc link
WORKDIR /etc/apache2/sites-enabled/
RUN ln -s ../sites-available/inforex.conf inforex.conf

COPY setup.sh /bin
COPY sample.config.local.php /bin
RUN chmod -R +x /bin/setup.sh

COPY php.ini /usr/local/etc/php/conf.d

RUN apt-get update -y && apt-get install supervisor -y
RUN mkdir -p /var/lock/apache2 /var/run/apache2 /var/log/supervisor
COPY supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

CMD /bin/setup.sh \
       && /usr/bin/supervisord

