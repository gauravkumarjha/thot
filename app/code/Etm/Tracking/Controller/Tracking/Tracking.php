<?php
namespace Etm\Tracking\Controller\Tracking;

use Magento\Framework\App\Action\Action;  
use Magento\Framework\App\Action\Context;  
use Magento\Framework\Controller\Result\JsonFactory; 
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\ObjectManager;

class Tracking extends \Magento\Framework\App\Action\Action
{
    protected $resultJsonFactory;  
    protected $customerSession; 
    protected $cookieManager;
    protected $cookieMetadataFactory;
   public function __construct(
       \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
       \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory,
       Context $context, JsonFactory $resultJsonFactory, CustomerSession $customerSession)  
    {  
        parent::__construct($context);  
        $this->resultJsonFactory = $resultJsonFactory;  
        $this->customerSession = $customerSession; 
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
    }  

        public function execute()  
        {

             $post = $this->getRequest()->getParams();
            if ($this->getRequest()->isGet())  {   
                $name = '';   
                $email = '';  
                $entity_id = '';  

                if ($this->customerSession->isLoggedIn()) {   
                    $name =  $this->customerSession->getCustomer()->getName();  
                    $email = $this->customerSession->getCustomer()->getEmail();  
                    $entity_id = $this->customerSession->getCustomer()->getData('entity_id');  
                }  

                $objectManager = ObjectManager::getInstance();  
                $etMediaNumber = substr(md5(uniqid() . mt_rand()), 0, 7);  

                $cookieName = "ctmvalue";  
                $ctmvalue = $this->cookieManager->getCookie("ctmvalue");    
                $_fbp = $this->cookieManager->getCookie("_fbp");    
                $_ga = $this->cookieManager->getCookie("_ga");    
                $_fbc = $this->cookieManager->getCookie("_fbc");    
                $_gcl_aw = $this->cookieManager->getCookie("_gcl_aw");   

                if (empty($ctmvalue)) {  
                    $metadata = $this->cookieMetadataFactory  
                                     ->createPublicCookieMetadata()  
                                     ->setDuration(365 * 24 * 60 * 60) // Duration in seconds  
                                     ->setSecure(true)   
                                     ->setPath('/')  
                                     ->setHttpOnly(false);  
                    $this->cookieManager->setPublicCookie($cookieName, $etMediaNumber, $metadata);  
                }

                 $post = $this->getRequest()->getParams();
                $pageRefrer = $post['pageRefrer'];  
                $pageUrl = $post['pageUrl'];  
                $userIp = $post['userIp'];  
                $userAgent = $post['userAgent']; 
            
                $itemsInfo[] =  array(  
                    'userAgent' => $userAgent,  
                    'userIp' => $userIp,  
                    'pageUrl' => $pageUrl,  
                    'pageRefrer' => $pageRefrer,  
                    '_gcl_aw' => $_gcl_aw,  
                    '_fbc' => $_fbc,             
                    '_ga' => $_ga,             
                    '_fbp' => $_fbp,             
                    'ctmvalue' => $ctmvalue,   
                    'name' => $name,  
                    'entity_id' => $entity_id    
                );   

                //$url  = "https://whisperinghomes.easyinsights.in/ping/";    
                // $this->sendDataToEtmedia($itemsInfo, $url);  
                $result = $this->resultJsonFactory->create(); 
                return $result->setData(['success' => true]);  
            }  
            $result = $this->resultJsonFactory->create();  
            return $result->setData(['success' => false, 'message' => 'Invalid Request']);  
        }  
    
    
    public function sendDataToEtmedia($itemsInfo, $url)
    {
       
        $jsonData = json_encode($itemsInfo); 
        $ch = curl_init($url);  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return the transfer as a string  
        curl_setopt($ch, CURLOPT_POST, true); // Send POST requests  
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData); // Set the POST fields  
        curl_setopt($ch, CURLOPT_HTTPHEADER, [  
        'Content-Type: application/json', // Set content type to application/json  
        'Content-Length: ' . strlen($jsonData), // Set content length  
        ]);  
        
        $response = curl_exec($ch); 
        curl_close($ch); 
        return true;
    }
}
?>