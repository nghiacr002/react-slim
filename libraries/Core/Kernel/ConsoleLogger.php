<?php 
namespace LegoAsync\Kernel;
//TODO
//implement output to console
class ConsoleLogger
{
    protected static $logs = null;
    public function __construct()
    {
        static::$logs = [];
    }
    /**
     * Add message to console_log ?
     * @param string $message
     */
    public static function add($message)
    {
        if(static::$logs == null)
        {
            static::$logs = [];
        }
        static::$logs[] = $message;
    }
    /**
     * Reset cache log
     */
    public static function reset()
    {
        static::$logs = [];
    }
    /**
     * Pull log
     * @return string
     */
    public static function pull($reset = true)
    {
        if(!is_array(static::$logs) || count(static::$logs) <= 0)
        {
            return "";
        }
        $message = implode(static::$logs,PHP_EOL);
        if($reset)
        {
            static::reset();
        }
        return $message;
    }
}