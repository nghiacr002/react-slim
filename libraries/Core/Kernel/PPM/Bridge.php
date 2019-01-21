<?php 
namespace LegoAsync\Kernel\PPM;
use PHPPM\Bridges\BridgeInterface;
use Psr\Http\Message\ServerRequestInterface;
use LegoAsync\Kernel\Processor;

class Bridge implements BridgeInterface
{
    protected $processor;
    protected $root;
    protected $settings;
    public function __construct()
    {
        $this->root = dirname(__DIR__, 4);
        require_once $this->root.'/src/init.php';
        $settings = require APP_SETTING_PATH. 'application.php';
        if($settings['settings']['mode'] == "development")
        {
            require_once APP_SOURCE_PATH.'Settings/development.php';
        }
        else
        {
            require_once APP_SOURCE_PATH.'Settings/production.php';
        }
        $this->settings = $settings;
        $this->processor = new Processor($settings);
    }
    /**
     * 
     * {@inheritDoc}
     * @see \PHPPM\Bridges\BridgeInterface::bootstrap()
     */
    public function bootstrap($appBootstrap, $appenv, $debug)
    {
        if(!($this->processor instanceof Processor))
        {
            throw new \RuntimeException("Could not init processor instance");
        }
        $this->processor->setProcessorEnv([
            'appBootstrap' => $appBootstrap, 
            'appenv' => $appenv, 
            'debug' => $debug, 
        ]);
        //$this->processor->createBaseApp(); 
    }
    /**
     * 
     * {@inheritDoc}
     * @see \Interop\Http\Server\RequestHandlerInterface::handle()
     */
    public function handle(ServerRequestInterface $request)
    {
        return $this->processor->handleRequest($request);
    }
}