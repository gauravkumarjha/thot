<?php 
namespace Chetaru\Article\Block\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface; 

class Article extends Template implements BlockInterface {
	protected $_urlBuilder;
	protected $_varFactory;
	public function __construct(
        \Magento\Backend\Block\Template\Context $context,        
        \Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\UrlInterface $urlBuilder, 
		\Magento\Variable\Model\VariableFactory $varFactory,		
        array $data = []
    )
    {        
        $this->_storeManager = $storeManager;  
		$this->_urlBuilder = $urlBuilder;	
		$this->_varFactory = $varFactory;		
        parent::__construct($context, $data);
		$this->setTemplate('widget/articles.phtml');
    }

	public function getArticles(){
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$connection = $resource->getConnection();
		$tableName = $resource->getTableName('chetaru_article_post'); //gives table name with prefix
		 
		//Select Data from table
		$sql = "SELECT * FROM " . $tableName . " WHERE status=1 ORDER BY article_date DESC";
		$result = $connection->fetchAll($sql);
		
		return $result;
	}
	public function getBaseUrl()
    {
        //return $this->_urlBuilder->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]).$this->_subDir.'/file';
		return $this->_urlBuilder->getBaseUrl();
		 
	}
	
    /**
     * get base image dir
     *
     * @return string
     */
    public function getBaseDir()
    {
        //return $this->_fileSystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath($this->_subDir.'/file');
		return $this->_fileSystem->getDirectoryWrite();
	}
	public function getAssitContactNumber() { 
        $var = $this->_varFactory->create();
        $var->loadByCode('press_contact_number');
        return $var->getValue('text');
    }
	public function getPressEmail() { 
        $var = $this->_varFactory->create();
        $var->loadByCode('press_email');
        return $var->getValue('text');
    }
}