Inforex
=======

[![License: LGPL v3](https://img.shields.io/badge/License-LGPL%20v3-blue.svg)](https://www.gnu.org/licenses/lgpl-3.0)

Copyright (C) Wrocław University of Science and Technology (PWr), 2013-2021. 
All rights reserved.

Developed within [CLARIN-PL](http://clarin-pl.eu/) project.


Installation and setup
======================

Dev-mode using docker
---------------------

The dependencies are installed within Docker container 
and the Inforex source code is linked to the container as an external storage. 

Before building the docker install *Composer*, *Docker* and *Docker Compose* running the following command:

```bash
sudo apt-get install composer docker docker-compose
```
Than build the docker by executing the following script. 

```bash
./docker-dev-up.sh
```

Links:
* http://localhost:9080/inforex — default admin account admin/admin,
* http://localhost:7080 — phpMyAdmin with default an account inforex/password.   

When new source files are added it is required to reload the composer dependencies 
by executing the following command:

```bash
composer update
```

Local installation
------------------

### Dependencies

#### A) Tools and libraries

* zlib      (zlib1g, zlib1g-dev)
* PHP5.6    (php5.6, php5.6-dev, php5.6-zip, php5.6-gd, php5.6-soap) 
* Apach2    (apache2) 
* MySQL 5.x (mysql-server) 
* Composer  (composer)
 
#### B) PHP module (xdiff)

  
   1. Install re2c library

      ```bash
        sudo apt-get install re2c
      ```
        
   2. Install libxdiff library 

      ```bash
        wget http://www.xmailserver.org/libxdiff-0.23.tar.gz
        tar -xvf libxdiff-0.23.tar.gz
        cd libxdiff-0.23
        ./configure
        make
        sudo make install
        sudo ldconfig
       ```
          
   3. Install xdiff PECL module

      ```bash
        sudo apt-get install php5.6-dev
        sudo pear install http://pecl.php.net/get/xdiff-1.5.2.tgz
      ```

   4. Enable xdiff module for PHP
     
      Insert following line into files:
      * /etc/php/5.6/apache2/php.ini
      * /etc/php/5.6/cli/php.ini
      
      ```ini
      extension=xdiff.so
      ```
         
   5. Restart Apache2

        ```bash
        sudo service apache2 reload
        ```

#### C) Generate autoload

```
composer install
```

In case of update:

```
composer update
```



### Set-up folder access

     
Set access to folder engine/templates_c. Execute the following commands
inside the inforex-{VERSION} folder:

```bash
   mkdir engine/templates_c
   chmod g+rwx engine/templates_c
   sudo chown :www-data engine/templates_c
```


### Set-up database


Create a new database and load `database/init/inforex-v1.0.sql` with the following command:

```sql
  CREATE DATABASE inforex;
  CREATE USER 'inforex'@'localhost' IDENTIFIED BY 'password';
  GRANT ALL PRIVILEGES ON inforex.* to inforex@localhost ;
```

```bash
  mysql -u inforex inforex < database/init/inforex-v1.0.sql
```

### Set-up HTTP access


Use one of the following methods.

#### A) Symbolic link

Create symbolic link to the public_html folder using following command

```bash
  sudo ln -s $PWD/public_html /var/www/inforex  
```

#### B) Virtual host

Create a new virtual host file:

```bash
  sudo vi /etc/apache2/sites-available/inforex.conf
```

with the following content:

```
  Alias /inforex /PATH_INFOREX/public_html
  <Directory /PATH_INFOREX/public_html>
    Require all granted
  </Directory>
```

and make a symbolic link:

```bash
  cd /etc/apache2/sites-enabled/
  sudo ln -s ../sites-available/inforex.conf inforex.conf
``` 

### Setup MySql

```bash
sudo vi /etc/mysql/mysql.conf.d/mysqld.cnf
```

```bash
[mysqld]  
sql_mode = "STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION"
```

```bash
sudo service mysql restart
```

### Configure Inforex


Open engine/config.php file and set the following parameters:

```php
    static private $path_engine       = '/path/inforex-{VERSION}/engine';
    static private $path_www          = '/path/inforex-{VERSION}/public_html'; 
    static private $path_secured_data = '/path/inforex-{VERSION}/data';

    static private $url = 'http://SET_VALUE_domain/inforex';
    static private $dsn = array(
            'phptype'  => 'mysql',
            'username' => '',
            'password' => '',
            'hostspec' => 'localhost',
            'database' => '',
    );
```   

### Login
Default admin account:
```
Login: admin
Password: admin
```
   
