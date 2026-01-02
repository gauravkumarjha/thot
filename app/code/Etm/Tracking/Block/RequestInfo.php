<?php  
namespace Etm\Tracking\Block;  

use Magento\Framework\View\Element\Template;  
use Magento\Framework\App\Request\Http as Request;  
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Model\Session;
 

class RequestInfo extends Template  
{  
    protected $request;  
    protected $storeManager;  
    protected $customerSession;  

    public function __construct(  
        Template\Context $context,  
        Request $request,  
        StoreManagerInterface $storeManager,  
        Session $customerSession,
        array $data = []  
    ) {  
        $this->request = $request;  
        $this->storeManager = $storeManager;  
        $this->customerSession = $customerSession;  
        

        parent::__construct($context, $data);  
    }  

    public function getBaseUrl()  
    {  
        return $this->storeManager->getStore()->getBaseUrl();  
    }  

    public function getPageUrl()  
    {  
        return $this->request->getUri();  
    }  

    public function getPageReferrer()  
    {  
        return $this->request->getServer('HTTP_REFERER');  
    }  

    public function getUserIp()  
    {  
        return $this->request->getClientIp();  
    }  

    public function getUserAgent()  
    {  
        return $this->request->getServer('HTTP_USER_AGENT');  
    }  
    
    public function getCustomerEmail()  
    {  
        if ($this->customerSession->isLoggedIn()) {  
            return $this->customerSession->getCustomer()->getEmail();  
        }  
        return null;  
    }
    public function getCustomerName()  
    {  
        if ($this->customerSession->isLoggedIn()) {  
            return $this->customerSession->getCustomer()->getName();  
        }  
        return null;  
    }

}