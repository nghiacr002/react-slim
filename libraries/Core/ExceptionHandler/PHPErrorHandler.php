<?php
 
namespace LegoAsync\ExceptionHandler; 
 
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Monolog\Logger;
 
final class PHPErrorHandler extends \Slim\Handlers\PhpError
{
    protected $logger;
 
    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }
 
    public function __invoke(Request $request, Response $response, \Throwable $exception)
    {
        // Log the message
        $this->logger->addCritical($this->renderThrowableAsText($exception));
 
        return parent::__invoke($request, $response, $exception);
    }
    
}