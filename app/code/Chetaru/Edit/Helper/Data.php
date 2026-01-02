<?php

namespace Chetaru\Edit\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    protected $storeManager;
    protected $objectManager;
	protected $_filterProvider;
    const XML_PATH_HELLOWORLD = 'inedit/';



    public function __construct(Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
		\Magento\Backend\Model\UrlInterface $backendUrl,
		\Magento\Cms\Model\Template\FilterProvider $filterProvider
    ) {
        $this->objectManager = $objectManager;
        $this->storeManager  = $storeManager;
		$this->_backendUrl = $backendUrl;
		$this->_filterProvider = $filterProvider;
        parent::__construct($context);
    }

    public function getConfigValue($field, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            $field, ScopeInterface::SCOPE_STORE, $storeId
        );
    }


    public function getGeneralConfig($code, $storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_HELLOWORLD . $code, $storeId);
    }
	
	public function getProductsGridUrl()
    {
        return $this->_backendUrl->getUrl('/post/products', ['_current' => true]);
    }
	public function filter($str)
    {
        $html = $this->_filterProvider->getPageFilter()->filter($str);
        return $html;
    }


}