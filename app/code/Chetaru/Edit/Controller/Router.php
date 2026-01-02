<?php

namespace Chetaru\Edit\Controller;
/**
 * Class Router
 * @package Chetaru\Blog\Controller
 */
class Router implements \Magento\Framework\App\RouterInterface
{
    /**
     * @var \Magento\Framework\App\ActionFactory
     */
    public $actionFactory;
    /**
     * @var \Chetaru\Edit\Helper\Data
     */
    public $helper;
    protected $_request;
    /**
     * @param \Magento\Framework\App\ActionFactory $actionFactory
     * @param \Chetaru\Blog\Helper\Data $helper
     */
    public function __construct(
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Chetaru\Edit\Helper\Data $helper
    ) {
    
        $this->actionFactory = $actionFactory;
        $this->helper        = $helper;
    }
    /**
     * @param $controller
     * @param $action
     * @param array $params
     * @return \Magento\Framework\App\ActionInterface
     */
    public function _forward($controller, $action, $params = [])
    {
		          $this->_request->setControllerName($controller)
              ->setActionName($action)
             ->setPathInfo('/inedit/' . $controller . '/' . $action);
          foreach ($params as $key => $value) {
              $this->_request->setParam($key, $value);
          }
		  
          return $this->actionFactory->create('Magento\Framework\App\Action\Forward');
		 
    }
    /**
     * Validate and Match Cms Page and modify request
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return bool
     */
    public function match(\Magento\Framework\App\RequestInterface $request)
    {
		
		 $this->_request = $request;
          $identifier = trim($request->getPathInfo(), '/');
		  $routePath  = explode('/', $identifier);
	      $params=array();
		  $module = array_shift($routePath);
		  switch ($module) {
			case 'inedit':
				 $controller = array_shift($routePath);
				 
					 $parameter_name = array_shift($routePath);
					 if($parameter_name=="category"){
						 $parameter_value = array_shift($routePath);
						$params = array($parameter_name=>$parameter_value);
						 $request->setModuleName('inedit')
							->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $identifier);
						 if(!empty($parameter_value)){
							 return $this->_forward('index', 'index',$params);
						 }
					 }
					 if($parameter_name=="view"){
						 $parameter_value = array_shift($routePath);
						 if(!empty($parameter_value)){
							 $params = array('url_key'=>$parameter_value);
							 $request->setModuleName('inedit')
								->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $identifier);
								 return $this->_forward('index', 'view',$params);
						 }
					 }
				 
                break;
		  }
    }
}