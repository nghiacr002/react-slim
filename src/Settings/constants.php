<?php 

define('DS',DIRECTORY_SEPARATOR);

define('APP_ROOT_PATH',dirname(dirname(dirname(__FILE__)))  . DS);
define('APP_SOURCE_PATH',APP_ROOT_PATH . DS .'src'. DS);
define('APP_LIB_PATH',APP_ROOT_PATH. 'libraries'. DS);
define('APP_LOG_PATH',APP_ROOT_PATH. 'logs'. DS);
define('APP_RESOURCE_PATH',APP_ROOT_PATH. 'resources'. DS);
define('APP_PUBLIC_PATH',APP_ROOT_PATH. 'public'. DS);
define('APP_DEBUG',true); 

define('APP_CACHE_PATH', APP_RESOURCE_PATH . 'Cache'. DS);
define('APP_SETTING_PATH', APP_SOURCE_PATH. 'Settings'. DS);
define('APP_TEMPLATE_PATH',APP_RESOURCE_PATH. 'Template'. DS);