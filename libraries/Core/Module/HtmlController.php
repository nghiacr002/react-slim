<?php 
namespace LegoAsync\Module;

use Slim\Container;
use Symfony\Component\Translation\Exception\RuntimeException;

class HtmlController extends  BaseController
{
    protected $view;
    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->setResponseContentType('text/html; charset=utf-8');
        $this->engine = $container->get('view'); 
        if(!($this->engine instanceof  \Slim\Views\Twig))
        {
            throw new RuntimeException("Twig render engine not found");
        }
    }
    /**
     * Render by template
     * @param array $data
     * @param string $template
     */
    public function render($data = [], $template = "")
    {
        return $this->view->render($this->response, $template, $data);
    }
}