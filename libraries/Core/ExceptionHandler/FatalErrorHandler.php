<?php 
namespace LegoAsync\ExceptionHandler; 
use Slim\Container;

class FatalErrorHandler
{
    protected $container; 
    public function __construct(Container $container = null)
    {
        $this->container = $container; 
    }
    /**
     * Register Fatal PHP error
     */
    public function register()
    {
        $exception = error_get_last();
        return $exception;
    }
}