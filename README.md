## ZF2 - Auth
**Version 1.1 Created by FlorentinTh - 2013**

### Introduction

ZF2 - Auth is a skeleton module for Zend Framework 2 that explain how to implement a simple authentification using sessions.
It's verry simple and easy to improve.

### What is possible ?

* Sign up
* Sign in
* Sign out
* Check username avaiability
* Edit profil

### Coming soon

* ~~Check username avaiability~~
* ~~Edit profil~~

### What is required ?

1. Clone this project
2. Import the database schema (see `database.sql`)

### Settings

1. Replace __your-database-name-here__ in `./config/autoload/global.php` :
	```php
    <?php

	return array(
	    'db' => array(
	        'driver'         => 'Pdo',
	        'dsn'            => 'mysql:dbname=your-database-name-here;host=localhost',
	        'driver_options' => array(
	            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
	        ),
	    ),
	    'service_manager' => array(
	        'factories' => array(
	            'Zend\Db\Adapter\Adapter'
	                    => 'Zend\Db\Adapter\AdapterServiceFactory',
	        ),
	    ),
	);
	```

2. Replace `xxxx` by your mysql connexion informations in `./config/autoload/local.php` :
	```php
	<?php
	return array(
	    'db' => array(
	        'username' => 'xxxx',
	        'password' => 'xxxx',
	    ),
	);
	```

Navigate to `http://yourproject/user/` and use this information to sign in :
	
		username : user1
		password : password