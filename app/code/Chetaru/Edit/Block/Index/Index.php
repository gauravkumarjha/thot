<?php
namespace Chetaru\Edit\Block\Index;
class Index extends \Magento\Framework\View\Element\Template
{
	protected $_categoryFactory;
	protected $_postFactory;
	protected $_urlBuilder;
	protected $_productCollectionFactory;
	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Magento\Framework\UrlInterface $urlBuilder,
		//\Chetaru\Edit\Model\CategoryFactory $categoryFactory,
		\Chetaru\Edit\Model\PostFactory $postFactory,
		\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
	)
	{
		//$this->_categoryFactory = $categoryFactory;
		$this->_postFactory = $postFactory;
		$this->_urlBuilder = $urlBuilder;
		$this->_productCollectionFactory = $productCollectionFactory;
		parent::__construct($context);
	}
	
    
	 public function getInEdit($id=null){
		 $parameters = $this->getRequest()->getParams();
		 if(isset($parameters['url_key'])){
			 $inedit = $this->_postFactory->create();
			 $in_edit = $inedit->getInEdit($parameters['url_key']);
			 return $in_edit;
		 }
		 
		 return "";
	 }
	 
}