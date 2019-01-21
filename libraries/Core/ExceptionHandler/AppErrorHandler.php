<?php
 
namespace LegoAsync\ExceptionHandler; 
 
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Monolog\Logger;
 
final class AppErrorHandler extends \Slim\Handlers\Error
{
    protected $logger;
 
    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }
 
    public function __invoke(Request $request, Response $response, \Exception $exception)
    {
        // Log the message
        $this->logger->addCritical($exception->getMessage(), [$exception->getTraceAsString()]);
 
        return parent::__invoke($request, $response, $exception);
    }
}