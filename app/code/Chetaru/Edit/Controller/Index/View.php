<?php
namespace Chetaru\Edit\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
class View extends \Magento\Framework\App\Action\Action
{
	protected $_pageFactory;
	protected $_postModel;
	public function __construct(
		Context $context,
        PageFactory $resultPageFactory//,
		//\Chetaru\Edit\Model\Post $postModel
	)
	{
		parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
		//$this->_postModel = $postModel;
		
	}

	public function execute($name=null)
	{
		$resultPageFactory = $this->resultPageFactory->create();
 
        // Add page title
      
	
	
        // Add breadcrumb
        /** @var \Magento\Theme\Block\Html\Breadcrumbs */
        $breadcrumbs = $resultPageFactory->getLayout()->getBlock('breadcrumbs');
        $breadcrumbs->addCrumb('home',
            [
                'label' => __('Home'),
                'title' => __('Home'),
                'link' => $this->_url->getUrl('')
            ]
        );
        
		$url_key = $this->getRequest()->getParam('url_key', false);
		$in_edit_home_link="";
		if(!empty($url_key)){
			// Instance of object manager 
			
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
			$resource = $objectManager->get('Magento\Framework\App\ResourceConnection'); 
			$connectionDb = $resource->getConnection();
			//gives table name with prefix 
			$tableName = $resource->getTableName('chetaru_edit_post');
			//Select Data from table 
			$query = "Select name, meta_title,meta_keywords,meta_description FROM " . $tableName . ' WHERE url_key="'.$url_key.'"'; 
			$result = $connectionDb->fetchRow($query); 

			if(!empty($result) && isset($result['name'])){

			
				if(!empty($result['meta_title'])) {
				$resultPageFactory->getConfig()->getTitle()->set(__($result['meta_title']));
				} else {
					$resultPageFactory->getConfig()->getTitle()->set(__($result['name']));
				}
				if(!empty($result['meta_title'])) {
			    	$resultPageFactory->getConfig()->setMetaTitle($result['meta_title']);
				}
				if (!empty($result['meta_keywords'])) {
					$resultPageFactory->getConfig()->setKeywords($result['meta_keywords']);
				}
				if (!empty($result['meta_description'])) {
					$resultPageFactory->getConfig()->setDescription($result['meta_description']);
				}

				$breadcrumbs->addCrumb('inedit_home',
					[
						'label' => __('Edits'),
						'title' => __('Edits'),
						'link' => $this->_url->getUrl('inedit')
					]
				);
				$breadcrumbs->addCrumb($url_key,
					[
						'label' => __($result['name']),
						'title' => __($result['name'])
					]
				);
			} else {
				$resultPageFactory->getConfig()->getTitle()->set(__('Edits'));
			}
		}
		
		
 
        return $resultPageFactory;
	}
}