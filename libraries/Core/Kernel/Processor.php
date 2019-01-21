<?php 
namespace LegoAsync\Kernel; 
use Psr\Http\Message\ServerRequestInterface;
use React\Promise\Promise;
use LegoAsync\ExceptionHandler\FatalErrorHandler;
use LegoAsync\Kernel\Application;
use React\EventLoop\LoopInterface;
use React\EventLoop\Factory;
use React\Http\Response;
class Processor
{
    protected $appSettings; 
    protected $server;
    protected static $loop;
    protected static $app;
    protected $numberOfRun; 
    protected $pid;
    protected $envConfigurations;
    /**
     * 
     * @param array $appSettings
     * @param LoopInterface $loop
     */
    public function __construct($appSettings = [], LoopInterface $loop = null)
    {
        $this->appSettings = $appSettings; 
        $this->pid = getmypid();
        $this->numberOfRun = 0; 
    }
    /**
     * Get ENV from worker
     * @return array
     */
    public function getProccessorEnv()
    {
        return $this->envConfigurations;
    }
    /**
     * Set processorEnv
     * @param array $envConfiguration
     */
    public function setProcessorEnv($envConfiguration = [])
    {
        $this->envConfigurations = $envConfiguration;
    }
    /**
     * Get instance worker Id
     * @return number
     */
    public function getPID()
    {
        return $this->pid;
    }
    /**
     * Set Event loop to processor
     * @param LoopInterface $loop
     * @return \LegoAsync\Kernel\Processor
     */
    public static function setLoop($loop)
    {
        static::$loop = $loop;
    }
    /**
     * Get Loop
     * @return LoopInterface
     */
    public static function getLoop()
    {
        return static::$loop;
    }
    /**
     * Create base app
     * @return Application
     */
    public function createBaseApp()
    {
        if(static::$app == null)
        {
            //console_log("Init App again ". date("Y-m-d h:i:s"));
            static::$app = new Application($this->appSettings);
            static::$app->registerRouters();
            static::$app->setProcessor($this);
            static::$app->setGlobal();
        }
        return static::$app;
    }
    /**
     * Initilize application
     * @return \LegoAsync\Kernel\Application
     */
    public function createAppInstance(ServerRequestInterface $request = null)
    {   
        $app = null;
        $app = $this->createBaseApp();
        if($app instanceof Application && $request != null)
        {   
            $app->registerEnvironment($request);
            $app->registerRequest($request);
            $app->registerResponse($request);
        }
        return $app;
    }
    public function count()
    {
        $this->numberOfRun++;
        return $this->numberOfRun;
    }
    /**
     * Check if Processor first boot
     * @return boolean
     */
    public function isFirstRun()
    {
        return ($this->numberOfRun == 1);
    }
    public function handleRequest(ServerRequestInterface $request = null)
    {
        $response = null;
        try 
        {
            $this->count();
            $app = $this->createAppInstance($request);
            $response =  $app->run(true);
            $messageLog = ConsoleLogger::pull();
            if(!empty($messageLog))
            {
                $body = $response->getBody();
                $body->write(PHP_EOL. "=======".PHP_EOL. $messageLog. PHP_EOL. "=======".PHP_EOL);
                $response = $response->withBody($body);
                if($app->getSetting('writeConsoleLogToFile') === true)
                {
                    $app->getContainer()['logger']->addDebug($messageLog);
                }
            }
            unset($app);
            
        } 
        catch (\Exception $e) 
        {
            $response = new \LegoAsync\Kernel\Http\Response();
            $response = $response->withJson(['msg' => $e->getMessage(),'code' =>$e->getCode()."[".$e->getLine()."]", 'trace' => $e->getTraceAsString()]);
        }
        return $response;
    }
}
