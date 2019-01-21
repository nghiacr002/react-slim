# react-slim
Bring ReactPHP, PHP-PM to Slim3 Framework
# Docs Ref
https://medium.com/async-php/co-operative-php-multitasking-ce4ef52858a0
http://nikic.github.io/2012/12/22/Cooperative-multitasking-using-coroutines-in-PHP.html
#Install PHP-PM
Recommand using PHP 7.2
Install PPM: PHP-PM (https://github.com/php-pm/php-pm/wiki/Use-without-Docker)
# Run Application
`cd to root_application`
`ppm start --bootstrap=LegoAsync --bridge=LegoAsync\\Kernel\\PPM\\Bridge --workers=1 --debug 1`
Start as nohub
`cd root_application/server`
`nohup ./start-ppm.sh > output 2>&1 &`