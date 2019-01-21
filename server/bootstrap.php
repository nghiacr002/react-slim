<?php 
use LegoAsync\Kernel\Processor;
use React\EventLoop\Factory;

require_once '../src/init.php';
//init server processor
$settings = require APP_SETTING_PATH. 'application.php';
if($settings['settings']['mode'] == "development")
{
    require_once APP_SOURCE_PATH.'Settings/development.php';
}
else
{
    require_once APP_SOURCE_PATH.'Settings/production.php';
}
$loop = Factory::create();
$processor = new Processor($settings, $loop);
$processor->handleResquest(null); //handle by default slim with apache or nginx