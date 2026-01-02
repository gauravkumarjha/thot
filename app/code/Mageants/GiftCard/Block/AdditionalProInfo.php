<?php
/**
 * @category Mageants GiftCard
 * @package Mageants_GiftCard
 * @copyright Copyright (c) 2016 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\GiftCard\Block;

use Magento\Framework\View\Element\Template\Context;
use Magento\Checkout\Model\Session;
use Mageants\GiftCard\Model\Giftquote;
use Mageants\GiftCard\Helper\Data;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Quote\Model\ResourceModel\Quote\Item\CollectionFactory as QuoteItemCollectionFactory;
use Mageants\GiftCard\Model\ResourceModel\Giftquote\CollectionFactory as GiftQuoteCollectionFactory;

/**
 * AdditionalProInfo class for add aditional info in product view page
 */
class AdditionalProInfo extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Magento\Framework\View\Element\Template\Context
     */
    protected $context;

    /**
     * @var Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;
    
    /**
     * @var Mageants\GiftCard\Model\Giftquote
     */
    protected $_giftquote;

    /**
     * @var Mageants\GiftCard\Helper\Data
     */
    protected $_helper;

    /**
     * @var Magento\Framework\Stdlib\CookieManagerInterface
     */
    protected $cookieManager;

    /**
     * @var Magento\Quote\Model\ResourceModel\Quote\Item\CollectionFactory
     */
    protected $_quoteItemCollectionFactory;

    /**
     * @var Mageants\GiftCard\Model\ResourceModel\Giftquote\CollectionFactory
     */
    protected $_giftQuoteCollectionFactory;
        
    /**
     * @param Context $context
     * @param Session $checkoutSession
     * @param Giftquote $quotes
     * @param Data $helper
     * @param CookieManagerInterface $cookieManager
     * @param QuoteItemCollectionFactory $quoteItemCollectionFactory
     * @param GiftQuoteCollectionFactory $giftQuoteCollectionFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Session $checkoutSession,
        Giftquote $quotes,
        Data $helper,
        CookieManagerInterface $cookieManager,
        QuoteItemCollectionFactory $quoteItemCollectionFactory,
        GiftQuoteCollectionFactory $giftQuoteCollectionFactory,
        array $data = []
    ) {
        $this->_checkoutSession = $checkoutSession;
        $this->_giftquote = $quotes;
        $this->_helper = $helper;
        $this->cookieManager = $cookieManager;
        $this->_quoteItemCollectionFactory = $quoteItemCollectionFactory;
        $this->_giftQuoteCollectionFactory = $giftQuoteCollectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * Return to Additional data
     *
     * @return string
     */
    public function getAdditionalData()
    {
        return "Gift Card Details";
    }
    
    /**
     * Return to gift quote items by customer id
     *
     * @param string $customerid
     * @return object
     */
    public function getGiftQuoteItems($customerid = '')
    {
        if ($customerid != ''):
            return $this->_giftquote->getCollection()->addFieldToFilter('customer_id', $customerid);
        endif;
    }

    /**
     * Return to card type by type id
     *
     * @param string $typeid
     * @return array
     */
    public function getCardType($typeid = '')
    {
        $cardtype=['0'=>'Virtual','1'=>'Printed', '2'=>'Combined'];
        return $cardtype[$typeid];
    }

    /**
     * Save gift  quote
     *
     * @param int $quoteid
     * @return void
     */
    public function saveQuote($quoteid = '')
    {
        if ($quoteid!=''):
            $this->_checkoutSession->setGiftquote($quoteid);
        endif;
    }
    
    /**
     * Get Customer Id by helper
     *
     * @return integer
     */
    public function getcustomerId()
    {
        return $this->_helper->getCustomerId();
    }

    /**
     * Get temp_customer_id
     *
     * @return array
     */
    public function geNotLoggedIntcustomerId()
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
        return $temp_customer_id;
    }

    /**
     * Return Not loggedIn gift quote items
     *
     * @param array $temp_customer_id
     * @return array
     */
    public function getNotLoggedInGiftQuoteItems(array $temp_customer_id)
    {
        $quoteId = $this->_checkoutSession->getQuote()->getId();
        $customer_collection = [];

        foreach ($temp_customer_id as $tmp_customer) {
            $quoteItemCollection = $this->_quoteItemCollectionFactory->create()->addFieldToFilter('quote_id', $quoteId);
            $giftQuoteCollection = $this->_giftQuoteCollectionFactory->create()
                                        ->addFieldToFilter('temp_customer_id', $tmp_customer);

            $giftQuoteCollection->getSelect()->joinLeft(
                ['quote_item' => $quoteItemCollection->getMainTable()],
                'main_table.product_id = quote_item.product_id',
                []
            );
            $customer_collection[] = $giftQuoteCollection->addFieldToFilter('quote_item.quote_id', $quoteId);
        }
        return $customer_collection;
    }

    /**
     * Get Currency Symbol
     *
     * @return string
     */
    public function getHelperCurrencySymbol()
    {
        return $this->_helper->getCurrencySymbol();
    }
}
