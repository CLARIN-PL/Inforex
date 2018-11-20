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
RUN apt-get install libmcrypt-dev zlib1g zlib1g-dev composer -y \
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

#Configure MySQL
#RUN echo 'sql_mode = "STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION"' | tee -a /etc/mysql/mysql.conf.d/#mysqld.cnf


#Create local config
# RUN echo "<?php \n \$config->dsn = array(\n    'phptype'  => 'mysql',\n    'username' => 'inforex', 'port' => '3306', 'password' => 'password',\n    'hostspec' => db,\n    'database' => 'inforex',\n);" | tee $inforex_location/engine/config.local.php

COPY docker/config.local.php $inforex_location/engine/config.local.php

COPY docker/setup.sh /bin
RUN chmod -R +x /bin/setup.sh

CMD /bin/setup.sh \
    && apache2-foreground
