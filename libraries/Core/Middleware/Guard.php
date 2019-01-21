<?php
namespace LegoAsync\Middleware; 
use Slim\Container;

class Guard
{
    protected $container;
    public function __construct(Container $container = null)
    {
        $this->container = $container;
    }
    /**
     * Example middleware invokable class
     *
     * @param  \Psr\Http\Message\ServerRequestInterface $request  PSR7 request
     * @param  \Psr\Http\Message\ResponseInterface      $response PSR7 response
     * @param  callable                                 $next     Next middleware
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke($request, $response, $next)
    {
        /*$route = $request->getAttribute("route");
        
        $methods = [];
        
        if (!empty($route)) 
        {
            $pattern = $route->getPattern();
            
            foreach ($this->router->getRoutes() as $route) 
            {
                if ($pattern === $route->getPattern()) 
                {
                    $methods = array_merge_recursive($methods, $route->getMethods());
                }
            }
            //Methods holds all of the HTTP Verbs that a particular route handles.
        } 
        else 
        {
            $methods[] = $request->getMethod();
        }*/
        $response = $next($request, $response);
        $settings = $this->container->get('settings'); 
        $guardSettings = isset($settings['guard']) ? $settings['guard'] : []; 
        $headers = isset($guardSettings['headers']) ? $guardSettings['headers'] : [];
        if(is_array($headers) && count($headers))
        {
            foreach($headers as $key => $value)
            {
                $response = $response->withHeader($key,$value);
            }
        }
        return $response;
    }
}
