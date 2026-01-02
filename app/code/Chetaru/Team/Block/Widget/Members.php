<?php 
namespace Chetaru\Team\Block\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface; 

class Members extends Template implements BlockInterface {
	protected $_urlBuilder;
	protected $_subDir = 'chetaru/team/member';
	public function __construct(
        \Magento\Backend\Block\Template\Context $context,        
        \Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\UrlInterface $urlBuilder,        
        array $data = []
    )
    {        
        $this->_storeManager = $storeManager;  
		$this->_urlBuilder = $urlBuilder;		
        parent::__construct($context, $data);
		$this->setTemplate('widget/members.phtml');
    }
	

	public function getBaseUrl()
    {
        //return $this->_urlBuilder->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]).$this->_subDir.'/file';
		return $this->_urlBuilder->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]);
    }
	
    /**
     * get base image dir
     *
     * @return string
     */
    public function getBaseDir()
    {
        //return $this->_fileSystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath($this->_subDir.'/file');
		return $this->_fileSystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
	}
	public function getMembers(){
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$connection = $resource->getConnection();
		$tableName = $resource->getTableName('chetaru_team_post'); //gives table name with prefix
		 
		//Select Data from table
		$sql = "Select * FROM " . $tableName . " WHERE status=1";
		$result = $connection->fetchAll($sql);
		
		return $result;
	}
}