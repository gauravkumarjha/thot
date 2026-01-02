<?php

namespace Dimedia\Utility\Controller\Checkout;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\Result\JsonFactory;

class Abandoned implements HttpGetActionInterface, HttpPostActionInterface
{
    protected $pageFactory = null;

    protected $_storeManager;

    protected $checkoutSession;

    protected $_resultJsonFactory;

    protected $_abandonedFactory;

    protected $request;

    protected $countryFactory;

    protected $quoteRepository;

    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        Session $checkoutSession,
        JsonFactory $resultJsonFactory,
        RequestInterface $request,
        \DiMedia\Utility\Model\AbandonedFactory $abandonedFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
    ) {
        $this->pageFactory = $pageFactory;
        $this->_storeManager = $storeManager;
        $this->checkoutSession = $checkoutSession;
        $this->_abandonedFactory = $abandonedFactory;
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->request = $request;
        $this->countryFactory = $countryFactory;
        $this->quoteRepository = $quoteRepository;
    }

    public function execute()
    {   
     
        try {
            $response = array();
            $result = $this->_resultJsonFactory->create();
            $params = $this->request->getParams();

            // print_r($params);
            // die;
            if (isset($params['email']) && filter_var($params['email'], FILTER_VALIDATE_EMAIL)) {
                $quote = $this->checkoutSession->getQuote();

                if (isset($quote) && $quote->getId()) {
                    $quoteItems = $quote->getAllVisibleItems();

                    $skus = array();
                    foreach ($quoteItems as $_item) {
                        $skus[]['sku'] = $_item->getSku();
                    }
                  
                    $email = "";
                    if (trim($params['email']) !== null) {
                        $email = trim($params['email']);
                        if ($email && !$quote->getCustomerEmail()) {
                            $quote->setCustomerEmail($email);
                            $this->quoteRepository->save($quote);
                        }
                    }

                    $customerName = "";
                    if (isset($params['customerName']) && trim($params['customerName']) !== null) {
                        $customerName = trim($params['customerName']);
                    }

                    $telephone = "";
                    if (isset($params['telephone']) && trim($params['telephone']) !== null) {
                        $telephone = trim($params['telephone']);
                    }

                    $telephone = "";
                    if (isset($params['telephone']) && trim($params['telephone']) !== null) {
                        $telephone = trim($params['telephone']);
                    }

                    $countryCode  = '';

                    // if (isset($_SERVER['HTTP_CF_IPCOUNTRY'])) {
                    //     $countryCode = $_SERVER['HTTP_CF_IPCOUNTRY'];
                    // }

                    $abandonedCart = $this->_abandonedFactory->create()->load($quote->getId(), 'cart_id');
                    $abandonedCart->setCartId($quote->getId());
                    $abandonedCart->setCustomerEmail($email);
                    $abandonedCart->setCustomerTelephone($telephone);
                    $abandonedCart->setCustomerName($customerName);
                    $abandonedCart->setSkus(serialize($skus));

                    if ($countryCode != '') {
                        $country = $this->countryFactory->create()->loadByCode($countryCode);
                        $countryName = $country->getName();
                        $abandonedCart->setCustomerCountry($countryName);
                    }

                    $abandonedCart->save();
                }
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        $response['success'] = 1;
        return $result->setData($response);
    }
}
