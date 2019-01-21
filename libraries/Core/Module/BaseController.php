<?php 
namespace LegoAsync\Module; 

use Slim\Http\Request;
use Slim\Container;
use React\EventLoop\LoopInterface;
use LegoAsync\Kernel\Http\Response;
use Slim\Http\Body;

class BaseController
{
    protected $request; 
    protected $response; 
    protected $container;
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->request = $container->get('request'); 
        if(!($this->request instanceof Request))
        {
            throw new \RuntimeException("Invalid controller");
        }
        $this->response = new Response();
    }
    /**
     * Get service from container
     * @param string $serviceName
     * @return mixed|null
     */
    public function getService($serviceName = "")
    {
        return $this->container->get($serviceName);
    }
    /**
     * Set default content type
     * @param string $contentType
     * @return \LegoAsync\Module\BaseController
     */
    public function setResponseContentType($contentType = 'application/json')
    {
        $this->response = $this->response->withHeader('Content-type', $contentType); 
        return $this;
    }
    /**
     * return HTML with template
     * @param array $data
     * @param string $template
     */
    public function toHtml($data = null)
    {
        $content = "";
        if(is_array($data))
        {
            $content = json_encode($data);
        }
        elseif(is_string($data))
        {
            $content = $data;
        }
        //TODO
        $this->response = $this->response->withBody($this->withBody($content));
        return $this->response;
    }
    /**
     * Return as XML content
     * @param array $data
     */
    public function ToXML($data = [])
    {
        //TODO
    }
    /**
     * To Json request
     * @param array $data
     * @return \Slim\Http\Response
     */
    public function toJson($data = [], $options = null)
    {
        $data = json_encode($data,$options);
        $this->response = $this->response->withBody($this->withBody($data));
        return $this->response;
    }
    /**
     * Get logger
     * @return \Monolog\Logger|NULL
     */
    public function getLogger()
    {
        return $this->getService('logger');
    }
    /**
     * Get Request
     * @return \Slim\Http\Request
     */
    public function getRequest()
    {
        return $this->request;
    }
    /**
     * Get Response
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }
    /**
     * 
     */
    protected function withBody($content)
    {
        $body = new Body(fopen('php://temp', 'r+'));
        $body->write($content);
        return $body;
    }
}