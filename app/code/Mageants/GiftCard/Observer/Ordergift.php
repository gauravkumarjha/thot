<?php
/**
 * @category Mageants GiftCard
 * @package Mageants_GiftCard
 * @copyright Copyright (c) 2016 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\GiftCard\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
use Mageants\GiftCard\Model\Giftquote;
use Psr\Log\LoggerInterface;
use Magento\Checkout\Model\Session;
use Mageants\GiftCard\Model\Customer;
use Mageants\GiftCard\Model\Account;
use Mageants\GiftCard\Model\Codeset;
use Mageants\GiftCard\Model\Codelist;
use Magento\Store\Model\StoreManagerInterface;
use Mageants\GiftCard\Helper\Data;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Directory\Model\CurrencyFactory;
use Mageants\GiftCard\Helper\CurrencyData;

/**
 * Order Gift observer for place order event
 */
class Ordergift implements ObserverInterface
{
    /**
     * @var Magento\Framework\App\RequestInterface
     */
    public $_request;

    /**
     * @var Mageants\GiftCard\Model\Giftquote
     */
    protected $_giftquote;

    /**
     * @var Psr\Log\LoggerInterface
     */
    protected $_logger;
    
    /**
     * @var Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;
    
    /**
     * @var Mageants\GiftCard\Model\Customer
     */
    protected $_customer;
    
    /**
     * @var Mageants\GiftCard\Model\Account
     */
    protected $_account;
    
    /**
     * @var Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    
    /**
     * @var Mageants\GiftCard\Model\Codeset
     */
    protected $_codeset;
    
    /**
     * @var Mageants\GiftCard\Model\Codelist
     */
    protected $_codelist;
    
    /**
     * @var Mageants\GiftCard\Helper\Email
     */
    protected $_email;
    
    /**
     * @var Mageants\GiftCard\Helper\Data
     */
    protected $_helper;

    /**
     * @var Magento\Framework\Stdlib\CookieManagerInterface
     */
    protected $cookieManager;

    /**
     * @var Magento\Directory\Model\CurrencyFactory
     */
    protected $_currencyFactory;

    /**
     * @var Mageants\GiftCard\Helper\CurrencyData
     */
    protected $_currencyData;

    /**
     * Constructor
     *
     * @param RequestInterface $request
     * @param Giftquote $giftquote
     * @param LoggerInterface $logger
     * @param Session $checkoutSession
     * @param Customer $customer
     * @param Account $account
     * @param Codeset $codeset
     * @param Codelist $codelist
     * @param StoreManagerInterface $storeManager
     * @param Data $helper
     * @param CookieManagerInterface $cookieManager
     * @param CurrencyFactory $currencyFactory
     * @param CurrencyData $currencyData
     */
    public function __construct(
        RequestInterface $request,
        Giftquote $giftquote,
        LoggerInterface $logger,
        Session $checkoutSession,
        Customer $customer,
        Account $account,
        Codeset $codeset,
        Codelist $codelist,
        StoreManagerInterface $storeManager,
        Data $helper,
        CookieManagerInterface $cookieManager,
        CurrencyFactory $currencyFactory,
        CurrencyData $currencyData
    ) {
        $this->_giftquote = $giftquote;
        $this->_logger = $logger;
        $this->_checkoutSession = $checkoutSession;
        $this->_request = $request;
        $this->_customer = $customer;
        $this->_account = $account;
        $this->_storeManager = $storeManager;
        $this->_codeset = $codeset;
        $this->_codelist = $codelist;
        $this->_helper = $helper;
        $this->cookieManager = $cookieManager;
        $this->_currencyFactory = $currencyFactory;
        $this->_currencyData = $currencyData;
    }

    /**
     * To check Order detail
     *
     * @param Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $quote_id = $this->_checkoutSession->getQuote()->getAllVisibleItems();
        $temp_customer_id = [];
        foreach ($quote_id as $cartItem) {
            $product_id = $cartItem->getProductId();
            $product_collection = $this->_giftquote->getCollection()->addFieldToFilter('product_id', $product_id)
                                    ->addFieldToFilter('quote_id', $cartItem->getQuote()->getId());
            foreach ($product_collection as $collection) {
                $temp_customer_id[] = $collection['temp_customer_id'];
            }
        }

        if ($this->_checkoutSession->getAccountid()!='' && $this->_checkoutSession->getGift()!=''):
            $updateOrder = $observer->getEvent()->getOrder();
            $updateOrder->setCouponRuleName('gift_certificate')->save();
            $updateOrder->setOrderGift($this->_checkoutSession->getGift())->save();
            $updateOrder->setGiftcardCode($this->_checkoutSession->getGiftCardCode());
            $updateOrder->setGiftcardAccountId($this->_checkoutSession->getAccountid())->save();
            
            $currencyCodeTo = $this->_currencyData->getCurrentCurrency();
            $currencyCodeFrom = $this->_currencyData->getBaseCurrency();
            $rate = $this->_currencyFactory->create()->load($currencyCodeTo)->getAnyRate($currencyCodeFrom);
            $updateOrder->setBaseOrderGift($rate*$this->_checkoutSession->getGift());
            $this->updateBalance();
            return;
        endif;
        
        if ($this->_checkoutSession->getGiftquote()):
            $order = $observer->getEvent()->getOrder();
            $order_id = $order->getIncrementId();
            $items =$order->getAllVisibleItems();
            $productIds = [];
            $gift_quotes = [];

            $quoteCollection = $this->_checkoutSession->getQuote()->getAllVisibleItems();
            $quoteid = '';
            foreach ($quoteCollection as $cartItem) {
                $quoteid = $cartItem->getQuote()->getId();
            }

            if ($this->_helper->getCustomerId()!==null) {
                $gift_quotes[] = $this->_giftquote->getCollection()->addFieldToFilter(
                    'customer_id',
                    $this->_helper->getCustomerId()
                )->addFieldToFilter(
                    'quote_id',
                    $quoteid
                );
            } else {
                foreach ($temp_customer_id as $temp_cust_id) {
                    $gift_quotes[] = $this->_giftquote->getCollection()->addFieldToFilter(
                        'temp_customer_id',
                        $temp_cust_id
                    )->addFieldToFilter(
                        'quote_id',
                        $quoteid
                    );
                }
            }
            $quote_id = $this->getItemdetail($items, $gift_quotes, $order_id);
            $this->_checkoutSession->unsGiftquote();
            $this->_checkoutSession->setGiftCardCode("");

            $this->_checkoutSession->setGift('');
            $this->_checkoutSession->setGifts('');
            $this->_checkoutSession->setGiftCardCode("");
            $this->_checkoutSession->setAccountid('');
            $this->_checkoutSession->setGiftbalance('');
        endif;
    }

    /**
     * To save item detail in quote id
     *
     * @param object $items
     * @param object $gift_quotes
     * @param object $order_id
     */
    public function getItemdetail($items, $gift_quotes, $order_id)
    {
        $quote_value = [];
        foreach ($gift_quotes as $quote) {
            $quote_data =  $quote->getData();
            foreach ($quote_data as $quote_data_value) {
                $quote_value[] = $quote_data_value;
            }
        }

        $quote_id=[];
        foreach ($items as $item) {
            if ($item->getProductType()=='giftcertificate') {
                foreach ($quote_value as $gift) {
                    $ids = [];
                    if ($gift['product_id']==$item->getProductId()) {
                        $quote_id[]=$gift['id'];
                        $codesetModel=$this->_codeset->getCollection()->addFieldToFilter(
                            'code_title',
                            trim($gift['codesetid'])
                        );
                        foreach ($codesetModel as $codeset) {
                            $id=$codeset->getId();
                            $order_id =  $this->callApplyCode($id, $order_id, $gift);
                        }
                    }
                }
            }
            
            if (!empty($quote_id)):
                foreach ($quote_id as $id) {
                    $quote=$this->_giftquote->load($id);
                    $quote->setOrderIncrementId($order_id);
                    $quote->save();
                }
            endif;
        }
        return $quote_id;
    }

    /**
     * To set customer data
     *
     * @param array $ids
     * @param object $order_id
     * @param object $gift
     */
    public function applyCode($ids, $order_id, $gift)
    {
        $codes=$this->_codelist->getCollection()->addFieldToFilter('code_set_id', $ids);
        $applicableCodes='';
        foreach ($codes as $giftcode) {
            if ($giftcode->getAllocate()==0):
                $applicableCodes=$giftcode->getCode();
                $code_list_id=$giftcode->getCodeListId();
                if ($code_list_id):
                    try {
                        $updatecode=['code_list_id'=>$code_list_id,'allocate'=>1];
                        $this->_codelist->setData($updatecode);
                        $this->_codelist->save();
                    } catch (Exception $e) {
                        $this->messageManager->addError(__("We can't find the code list id."));
                    }
                endif;
                break;
            endif;
        }
        $certificateCode=[];
        if ($applicableCodes!=''):
            $certificateCode[]=$applicableCodes;
            $customerdata=
            [
                'code_value'=>$gift['gift_card_value'],
                'card_type'=>$gift['card_types'],
                'sender_name'=>$gift['sender_name'],
                'sender_email'=>$gift['sender_email'],
                'recipient_name'=>$gift['recipient_name'],
                'recipient_email'=>$gift['recipient_email'],
                'date_of_delivery'=>$gift['date_of_delivery'],
                'message'=>$gift['message'],
                'order_id'=>$order_id,
                'timezone'=>$gift['timezone'],
                'emailtime'=>$gift['emailtime']
            ];

            $this->_customer->setData($customerdata);
            $orderid=$this->_customer->save()->getId();
            $accountdata=
            [
                'order_id'=>$orderid,
                'status'=>'0',
                'website'=>$this->_storeManager->getStore()->getWebsiteId(),
                'initial_code_value'=>$gift['convert_gift_card_value'],
                'current_balance'=>$gift['convert_gift_card_value'],
                'comment'=>$gift['message'],
                'gift_code'=>$applicableCodes,
                'expire_at'=>$gift['expiry_date'],
                'template'=>$gift['template_id'],
                'customer_id'=>$gift['customer_id'],
                'categories'=>$gift['categories'],
                'custom_upload'=>$gift['custom_upload'],
                'sendtemplate_id'=>$gift['sendtemplate_id'],
                'order_increment_id'=>$order_id
            ];
            $this->_account->setData($accountdata);
            $this->_account->save();
        endif;
            return $order_id;
    }

    /**
     * Update balance and unset session
     */
    public function updateBalance()
    {
        $status=1;
        if ($this->_checkoutSession->getGiftbalance()===0 || $this->_checkoutSession->getGiftbalance()=='0'):
            $status=0;
        endif;
        $accountdata=['status'=>$status,'current_balance'=>$this->_checkoutSession->
        getGiftbalance(),'account_id'=>$this->_checkoutSession->getAccountid()];
        $this->_account->setData($accountdata);
        $this->_account->save();
        $this->_checkoutSession->unsGiftbalance();
        $this->_checkoutSession->unsAccountid();
        $this->_checkoutSession->unsGift();
        $this->_checkoutSession->unsGiftCardCode();

        $this->_checkoutSession->setGift('');
        $this->_checkoutSession->setGifts('');
        $this->_checkoutSession->setGiftCardCode("");
        $this->_checkoutSession->setAccountid('');
        $this->_checkoutSession->setGiftbalance('');
    }

    /**
     * Call Apply Code Method
     *
     * @param array $id
     * @param object $order_id
     * @param object $gift
     */
    public function callApplyCode($id, $order_id, $gift)
    {
        if ((int)$id) {
            $ids[] = $id;
            $certificateCode = [];
            return $this->applyCode($ids, $order_id, $gift);
        }
    }
}
