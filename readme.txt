0.Need to read 
https://medium.com/async-php/co-operative-php-multitasking-ce4ef52858a0
http://nikic.github.io/2012/12/22/Cooperative-multitasking-using-coroutines-in-PHP.html

1. Install PPM: PHP-PM (https://github.com/php-pm/php-pm/wiki/Use-without-Docker)	
- Require php-cli ( recommand php7.2-cli)
- Open /etc/php5/cgi/php.ini, find line disable_functions = pcntl_alarm,pcntl_fork, ... and place a ; in front of it:
- Install following step
$ git clone https://github.com/php-pm/php-pm.git
$ cd php-pm
$ composer install
$ ln -s `pwd`/bin/ppm /usr/local/bin/ppm
$ ppm --help

2. Run application:
- cd to root_application 
- ppm start --bootstrap=LegoAsync --bridge=LegoAsync\\Kernel\\PPM\\Bridge --workers=1 --debug 1
- ppm start --help to get more information


nohup ./start-ppm.sh > output 2>&1 &