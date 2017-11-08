    Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
    Wrocław University of Technology

In this file you will find instruction how to install Inforex step by step. 
To find the license terms please see LICENSE.


Step 1: Install dependencies
============================

Inforex requires the following dependencies:

A) Applicatios
---------------
* PHP5.6    (php5, php5-dev) 
* Apach2    (apache2) 
* MySQL 5.x (mysql-server) 
* PEAR      (php-pear) 

B) PEAR modules
----------------
* Auth
* HTML_Common
* MDB2-2.5.0b5
* MDB2_Driver_mysql-1.4.1
* MDB2_TableBrowser-0.1.3
* HTTP_Session2-0.7.3
* Text_Diff  

To install PEAR module you can use the `pear` command as following:

```bash
sudo pear install <module>
```

C) PHP module (xdiff)
---------------------
  
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

        sudo service apache2 reload


Step 2: Set-up folder access
============================
     
Set access to folder engine/templates_c. Execute the following commands
inside the inforex-{VERSION} folder:

```bash
   mkdir engine/templates_c
   chmod g+rwx engine/templates_c
   sudo chown :www-data engine/templates_c
```


Step 3: Set-up database
=======================

Create a new database and load inforex.sql with the following command:

```sql
  CREATE DATABASE inforex;
  CREATE USER 'inforex'@'localhost' IDENTIFIED BY 'password';
  GRANT ALL PRIVILEGES ON inforex.* to inforex@localhost ;
```

```bash
  mysql -u inforex inforex < inforex.sql
```

Step 4: Set-up HTTP access
==========================

Use one of the following methods.

A) Symbolic link
-------------------------------------------------------------------------

Create symbolic link to the public_html folder using following command

```bash
  sudo ln -s $PWD/public_html /var/www/inforex  
```

B) Virtual host
---------------

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

Step 5: Configure Inforex
=========================

Open engine/config.php file and set the following parameters:

```php
    var $path_engine       = '/path/inforex-{VERSION}/engine';
    var $path_www          = '/path/inforex-{VERSION}/public_html'; 
    var $path_secured_data = '/path/inforex-{VERSION}/data';

    var $url = 'http://SET_VALUE_domain/inforex';
    var $dsn = array(
            'phptype'  => 'mysql',
            'username' => '',
            'password' => '',
            'hostspec' => 'localhost',
            'database' => '',
    );
```   

Step 6: Login
=============

There are two default user accounts:
* 'admin' with password 'admin' — user with administrator privileges,
* 'corpus' with password 'corpus' — owner of CEN corpora.
   
