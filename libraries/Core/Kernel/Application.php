<?php 
namespace LegoAsync\Kernel; 
use Slim\Http\Environment;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Headers;
use Slim\Container;
use LegoAsync\ExceptionHandler\AppErrorHandler;
use LegoAsync\ExceptionHandler\PHPErrorHandler;
use Slim\Http\StatusCode;
use LegoAsync\Middleware\Guard;
use LegoAsync\Kernel\Http\Response;
use React\EventLoop\Factory;
use React\EventLoop\LoopInterface;
use Illuminate\Database\Capsule\Manager;
use LegoAsync\Mailer\Mailer;
class Application extends \Slim\App
{
    protected $currentRequest;
    protected static $loopEvent; 
    protected $numberOfRun; 
    protected $processor;
    protected $callback;
    protected $scheduler;
    protected static $instance;
    public function __construct($container = [])
    {
        parent::__construct($container);
        $this->numberOfRun = 1;
        $this->registerExtraServices($container);
        //add simplest guard
        $this->add( new Guard($this->getContainer()));
        //add XFrame-Options
        $this->add(new \Clickjacking\Middleware\XFrameOptions());
    }
   
    /**
     * Check if Processor first boot
     * @return boolean
     */
    public function isFirstRun()
    {
        return ($this->numberOfRun == 1);
    }
    /**
     * Set processor
     * @param Processor $processor
     * @return \LegoAsync\Kernel\Application
     */
    public function setProcessor(Processor $processor)
    {
        $this->processor = $processor;
        return $this;
    }
    
    public function run($slient = false)
    {
        $this->increaseNumberOfRun();
        $response = null;
        try
        {
            $response = parent::run($slient);
            if($slient == false)
            {
                return;
            }
        }
        catch(\Exception $ex)
        {
            $response =  new Response(StatusCode::HTTP_BAD_GATEWAY);
            $body = $response->getBody();
            $body->write($ex->getMessage());
            $response = $response->withBody($body);
        }
        
        return $response;
    }
    /**
     * Get event loop
     * @return LoopInterface
     */
    public static function getLoop()
    {
        if(static::$loopEvent == null)
        {
            static::$loopEvent = Factory::create();
        }
        return static::$loopEvent;
    }
    /**
     * Register all module routers
     * @throws \ErrorException
     */
    public function registerRouters()
    {
        $routerConfigs = [];
        $modularSetting = $this->getSetting('module');
        $basePath = $modularSetting['basePath'];
        $items = @scandir($basePath);
        if(is_array($items) && count($items))
        {
            foreach($items as $item)
            {
                $path = $basePath. $item . DS;
                $routerFile = $path.'router.php';
                if(is_dir($path) && file_exists($routerFile))
                {
                    $routerFileConfigs = require $routerFile;
                    if(is_array($routerFileConfigs) && count($routerFileConfigs))
                    {
                        foreach($routerFileConfigs as $name => $config)
                        {
                            if(isset($routerConfigs[$name]) && $modularSetting['allowToOverWriteRouter'] == false)
                            {
                                throw new \ErrorException("route ".$name . " was defined before");
                            }
                            $routerConfigs[$name] = $config;
                        }
                    }
                }
            }
        }
        if(is_array($routerConfigs) && count($routerConfigs))
        {
            foreach($routerConfigs as $name => $config)
            {
                $hander = $config['controller'];
                if(!isset($config['action']) || empty($config['action']))
                {
                    $config['action'] = "Index";
                }
                $hander.= ":". $config['action'];
                $route = $this->map($config['method'],$config['uri'],$hander);
                $route->setName($name);
            }
        }
    }
    /**
     * Reset service from DI Container
     * @param string $name
     * @return \LegoAsync\Kernel\Application
     */
    public function resetService($name)
    {
        $this->getContainer()->offsetUnset($name);
        return $this;
    }
    /**
     * Register response
     * @param ServerRequestInterface $request
     */
    public function registerResponse(ServerRequestInterface $request)
    {
        $this->resetService('response'); 
        $this->getContainer()['response'] = function ($container){
           
            $httpVersion = $container->get('settings')['httpVersion'];
            $contentType = $container->get('settings')['defaultContentType'];
            $headers = new Headers(['Content-Type' => $contentType]);
            $response = new \Slim\Http\Response(StatusCode::HTTP_OK, $headers); 
            return $response->withProtocolVersion($httpVersion);
        };
    }
    /**
     * Register again enviroment from server parameters
     * @param ServerRequestInterface $request
     */
    public function registerEnvironment(ServerRequestInterface $request)
    {   
        $this->resetService('environment');
        $this->getContainer()['environment'] = function () use($request){
            return Environment::mock(array_merge($_SERVER,$request->getServerParams()));
        };
    }
    /**
     * Register again request from server parameters
     */
    public function registerRequest(ServerRequestInterface $request)
    {
        $this->resetService('request');
        $this->getContainer()['request'] = function ($container) use($request) {
            
            $method = $request->getMethod();
            $uri = $request->getUri();
            $headers = $request->getHeaders();
            $cookies = $request->getCookieParams();
            $serverParams = $request->getServerParams();
            $body = $request->getBody();
            $uploadedFiles = $request->getUploadedFiles();
            $headers = $request->getHeaders();
            $slimHeader = new Headers();
            foreach( $headers as $key=> $value)
            {
                $slimHeader->add($key, $value);
            }
            $slimRequest =  new \Slim\Http\Request($method, $uri, $slimHeader, $cookies, $serverParams, $body, $uploadedFiles);
            //hack slim to get method from ReactHttp Server request
            $slimRequest = $slimRequest->withMethod($method);
            if ($method === 'POST' &&
                in_array($slimRequest->getMediaType(), ['application/x-www-form-urlencoded', 'multipart/form-data'])
                ) {
                    // parsed body must be $_POST
                    $slimRequest = $slimRequest->withParsedBody($request->getParsedBody());
                }
            return $slimRequest;
        };
    }
    /**
     * Get all router configurations
     * @return NULL|mixed
     */
    public function getRouteConfigs()
    {
        return $this->getSetting('routerConfigs');
    }
    /**
     * Get router detail
     * @param mixed $name
     * @return NULL
     */
    public function getRouterConfig($name)
    {
        $configs = $this->getRouteConfigs();
        return isset($configs[$name]) ? $configs[$name] : null;
    }
    /**
     * Get all application settings
     * @return array
     */
    public function getSettings()
    {
        $container = $this->getContainer();
        return $container['settings'];
    }
    /**
     * Get detail setting by key
     * @param string $key
     * @return NULL|mixed
     */
    public function getSetting($key)
    {
        $settings = $this->getSettings();
        return isset($settings[$key]) ? $settings[$key] : null;
    }
    /**
     * Increase number
     * @return \LegoAsync\Kernel\Application
     */
    public function increaseNumberOfRun()
    {
        $this->numberOfRun++; 
        return $this;
    }
    /**
     * Get processor
     * @return \LegoAsync\Kernel\Processor
     */
    public function getProcessor()
    {
        return $this->processor;
    }
    /**
     * Set global
     */
    public function setGlobal()
    {
        static::$instance = $this;
    }
    /**
     * Get current instance
     * @return Application
     */
    public static function getInstance()
    {
        return static::$instance;
    }
    /**
     * Register extra services to Slim App
     * @param array $container
     */
    protected function registerExtraServices($container = [])
    {   
        //echo "Need to register-again". PHP_EOL;
        $settings = [];
        if($container instanceof Container)
        {
            $settings = $container->get('settings');
        }
        else
        {
            $settings = isset($container['settings']) ? $container['settings'] : array();
        }
        $this->getContainer()['logger'] = function($container) use($settings){
            $logger = new \Monolog\Logger('LegoAsync');
            $fileName = $settings['logger']['path'] . ".worker.". $this->getProcessor()->getPID();
            $fileHandler = new \Monolog\Handler\RotatingFileHandler($fileName,0,$settings['logger']['level'],true, 0777);
            $formatter = new \Monolog\Formatter\LineFormatter();
            $formatter->includeStacktraces();
            $fileHandler->setFormatter($formatter);
            $logger->pushHandler($fileHandler);
            return $logger;
        };
        $this->getContainer()['errorHandler'] = function ($container) {
            return new AppErrorHandler($container['logger']);
        };
        $this->getContainer()['phpErrorHandler'] = function ($container) {
            return new PHPErrorHandler($container['logger']);
        };
        $this->getContainer()['db'] = function ($container) use($settings) {
            $capsule = new Manager();
            $capsule->addConnection($settings['database']['default']);
            $capsule->setAsGlobal();
            $capsule->bootEloquent();
            
            return $capsule;
        };
        $this->getContainer()['view'] = function ($container) use($settings) {
            $templateSettings = $settings['template'];
            $view = new \Slim\Views\Twig($templateSettings['path'], [
                'cache' => ($templateSettings['cache'] == true ) ? APP_CACHE_PATH : false
            ]);
            $router = $container->get('router');
            $uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
            $view->addExtension(new \Slim\Views\TwigExtension($router, $uri));
            
            return $view;
        };
        $this->getContainer()['mailer'] = function($container) use($settings){
           $mailer = new Mailer($settings['mailer']);  
           return $mailer;
        };
    }
}