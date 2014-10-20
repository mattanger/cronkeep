<?php
namespace library\App;

use \Zend\View\Renderer\RendererInterface;
use \Zend\View\Resolver\ResolverInterface;

/**
 * Implementation of a layout system similar to what other framworks have.
 * Extends \Zend\View\Renderer\RendererInterface for interoperability with Zend_View.
 * 
 * @author Bogdan Ghervan <bogdan.ghervan@gmail.com>
 */
class View extends \Slim\View implements RendererInterface
{
	/**
	 * Local view helper cache.
	 * 
	 * @var array
	 */
	protected $_helperCache = array();
	
	/**
	 * View helper manager.
	 * 
	 * @var \Zend\View\HelperPluginManager
	 */
	protected $_helperManager = array();
	
	/**
	 * Class constructor.
	 * Initializes helper manager.
	 * 
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
		
		// Initialize helper manager
		$this->_helperManager = new \Zend\View\HelperPluginManager();
		$this->_helperManager->setRenderer($this);
		
		// Inject form-related invokable helpers
		$helperConfig = new \Zend\Form\View\HelperConfig();
		$helperConfig->configureServiceManager($this->_helperManager);
	}
	
	/**
	 * Returns the template engine object.
	 * 
	 * @return Layout
	 */
	public function getEngine()
	{
		return $this;
	}
	
	/**
	 * Renders template and injects it to the layout file.
	 * 
	 * @param string $template
	 * @param array $data
	 * @return string
	 */
	public function render($template, $data = null)
	{
		return parent::render($template, $data);
	}
	
    /**
     * Set the resolver used to map a template name to a resource the renderer may consume.
     *
     * @param  ResolverInterface $resolver
     * @return Layout
     */
	public function setResolver(ResolverInterface $resolver)
	{
		return $this;
	}
	
	/**
	 * Renders template fragment in its own veriable scope.
	 * 
	 * @param string $template
	 * @param array $data
	 * @return string
	 */
	public function partial($template, $data = array())
	{
		$view = new View();
		$view->setTemplatesDirectory($this->getTemplatesDirectory());
		
		return $view->render($template, $data);
	}
	
	/**
	 * Provides access to view helpers.
	 * 
	 * @param string $method
	 * @param array $args
	 * @return mixed
	 */
	public function __call($method, $args)
	{
		if (!isset($this->_helperCache[$method])) {
			$helper = $this->_helperManager->get($method);
			$helper->setView($this);
			
			$this->_helperCache[$method] = $helper;
		}
		
		return call_user_func_array($this->_helperCache[$method], $args);
	}
}