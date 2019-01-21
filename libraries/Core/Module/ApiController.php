<?php 
namespace LegoAsync\Module; 

use Slim\Container;

class ApiController extends BaseController
{
    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->setResponseContentType('application/json');
    }
}