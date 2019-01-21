<?php 
use LegoAsync\Kernel\ConsoleLogger;

require 'Settings/constants.php';

require_once APP_LIB_PATH.'vendor/autoload.php';
require_once APP_SOURCE_PATH.'autoload.php';
//require_once APP_LIB_PATH . 'Core/autoload.php';

if(defined('DEFAULT_SERVER_TIMEZONE'))
{
    @date_default_timezone_set(DEFAULT_SERVER_TIMEZONE);
}
//debug 
if(defined('APP_DEBUG') && APP_DEBUG == true)
{
    ini_set('display_startup_errors', 1);
    ini_set('display_errors', 1);
    error_reporting(-1);
}
else
{
    ini_set('display_startup_errors', 0);
    ini_set('display_errors', 0);
    error_reporting(0);
}
/**
 * Function debug
 */
if(!function_exists('d'))
{
    
    function d($info, $isDumping = false)
    {
        $isCLI = (PHP_SAPI == 'cli');
        (!$isCLI ? print '<pre style="text-align:left; padding-left:15px;">' : false);
        ($isDumping ? var_dump($info) : print_r($info));
        (!$isCLI ? print '</pre>' : false);
    }
}
if(!function_exists('console_log'))
{
    function console_log($info)
    {
        $message = var_export($info,true);
        //user_error($message);
        ConsoleLogger::add($message);
    }
}
if(!function_exists('read_cnf_file'))
{
    function read_cnf_file($filename = "")
    {
        $settings = parse_ini_file($filename,true);
        return $settings;
    }
}
if(!function_exists('forceSettingsFromENV'))
{
    /**
     * Get Value from ENV
     * @param array $settings
     */
    function forceSettingsFromENV($settings = [])
    {
        $results = $settings;
        foreach($settings as $key => $setting)
        {
            $keyENV = "ENV_".$key;
            $environmentValue = isset($_SERVER[$keyENV]) ?  $_SERVER[$keyENV] : null; //getenv($key,true);
            if($environmentValue === null)
            {
                continue;
            }
            $environmentValue = @base64_decode($environmentValue);
            if(strpos($environmentValue,"\"") !== false)
            {
                $environmentValue = str_replace('\\"','"',$environmentValue);
            }
            $decodedValue = @json_decode($environmentValue,true);
            if(!is_array($decodedValue))
            {
                $decodedValue = $environmentValue;
            }
            $results[$key] = $decodedValue;
        }
        return $results;
    }
}
