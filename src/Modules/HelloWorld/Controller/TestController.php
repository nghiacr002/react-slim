<?php 
namespace LegoAsync\Modules\HelloWorld\Controller; 

use LegoAsync\Module\ApiController;
use Clue\React\Mq\Queue;
use LegoAsync\Kernel\Http\Response;
use LegoAsync\Kernel\Application;
use LegoAsync\Modules\HelloWorld\Model\FoodItem;
class TestController extends ApiController
{
    public function Home()
    {
        $params = $this->request->getParams(); 
        $params['time'] = date('y-m-d H:i:s');
        $this->getLogger()->addInfo("Hello request ". microtime(true));
        return $this->toJson($params);
    }
    /**
     * Test Async Function
     * @return Response
     */
    public function TestAsync()
    {
        $loop = Application::getLoop();
        $browser = new \Clue\React\Buzz\Browser($loop);
        // set up two parallel requests
        $request1 = $browser->get('http://www.google.com/');
        $request2 = $browser->get('http://www.google.co.uk/');
        //will return & stop when one of requests finishes.
        $fasterResponse = \Clue\React\Block\awaitAny(array($request1, $request2), $loop);
        return $fasterResponse;
    }
    /**
     * Queue request as mutex
     * @return Response
     */
    public function TestQueue()
    {
        $loop = Application::getLoop();
        $browser = new \Clue\React\Buzz\Browser($loop); 
        $concurrency = 10; // 10 request un as sample time;
        $limit = null ; // maximum number of processes in queue, null = unlimited
        $queue = new Queue($concurrency, $limit, function($url) use($browser){
            return $browser->get($url);
        }); 
        $promises = [$queue('http://www.google.com/'), $queue('https://tinhte.vn/')];
        $responses = Block\awaitAll($promises, $loop);
        return $responses[1]; // return latest response
    }
    /**
     * Test DB Connection
     * @return \Slim\Http\Response
     */
    public function TestDBConnection()
    {
        //$db = $this->getService('db'); 
        $model = new FoodItem();
        $items = $model->newQuery()->first(); 
        //console_log($items->toArray());
        return $this->toJson($items);
    }
    /**
     * Try to dump as HTML
     * @return \LegoAsync\Modules\HelloWorld\Controller\TestController
     */
    public function TestPHPInfo()
    {
        ob_start();
        phpinfo();
        $content = ob_get_contents();
        ob_end_clean();
        //echo $content;
        $this->setResponseContentType('text/html');
        return $this->toHtml($content);
    }
}