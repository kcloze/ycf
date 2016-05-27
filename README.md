## ycf framework


* a very simple,micro PHP framework 
* it can be run in Cli,PHP-FPM,Swoole
* composer install and name space auto load
* a solution for micro-services and blazing fast APIs


## Requirements

* PHP 5.3+


## Installation

1. git clone https://github.com/kcloze/ycf.git your-app
2. cd your-app and run: composer install
2. chmod -R 777 src/runtime/
3. edit src/config/settings.ini.php for mysql config or redis,add test table(https://github.com/kcloze/ycf/blob/master/pdo_test.sql)

## How to run

###php-fpm  
run in php-fpm: Open your browser and enter http://youhost/index.php?ycf=hello&act=hello

if set nginx as follow,url can be simple: http://youhost/hello/hello

```
        location / {
                if (!-e $request_filename){
                         rewrite ^/(.*) /index.php last;
                 }
        }

```

###cli
####run in cli: 
* php index.php ycf=hello act=hello

###swoole
run with swoole:  
 * php env in you path 
 * chmod u+x server.sh
 * ./server.sh


## Documentation
 * in src/service/ ,you can add your business code here
 * Naming Conventions: 
 * ---service class name :YcfYourname.php
 * ---method name : public static function actionYourname()
 * [DB Class Use](doc/db.md)
 * if you need redis,shoud install phpredis extention(https://github.com/phpredis/phpredis)



## Benchmarks
[benchmarks](doc/benchmarks.md)


## Community
mail: pei.greet@gmail.com
qq群: 141059677


##License
The ycf framework is open-sourced software licensed under the MIT license






