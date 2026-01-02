<?php
/**
 * @category Mageants GiftCard
 * @package Mageants_GiftCard
 * @copyright Copyright (c) 2016 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\GiftCard\Plugin;

use Magento\Directory\Controller\Currency\SwitchAction as SwitchActionParent;
use Magento\Checkout\Model\Session;
use Magento\Checkout\Model\Cart;
use Magento\Directory\Model\CurrencyFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Session\SessionManagerInterface;
use Mageants\GiftCard\Helper\CurrencyData;

/**
 * Plugin SwitchAction
 */
class SwitchAction
{
    /**
     * @var Magento\Checkout\Model\Session
     */
    private $_checkoutSession;

    /**
     * @var Magento\Framework\App\Action\Contex
     */
    protected $context;

    /**
     * @var Magento\Checkout\Model\Cart
     */
    protected $cart;

    /**
     * @var CurrencyFactory
     */
    protected $currencyFactory;

    /**
     * @var Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Magento\Framework\Session\SessionManagerInterface
     */
    private $_sessionManagerInterface;

    /**
     * @var Mageants\GiftCard\Helper\CurrencyData
     */
    protected $_currencyData;

    /**
     * @param Session $checkoutSession
     * @param Cart $cart
     * @param CurrencyFactory $currencyFactory
     * @param StoreManagerInterface $storeManager
     * @param SessionManagerInterface $sessionManagerInterface
     * @param CurrencyData $currencyData
     */
    public function __construct(
        Session $checkoutSession,
        Cart $cart,
        CurrencyFactory $currencyFactory,
        StoreManagerInterface $storeManager,
        SessionManagerInterface $sessionManagerInterface,
        CurrencyData $currencyData
    ) {
        $this->_checkoutSession  = $checkoutSession;
        $this->cart              = $cart;
        $this->currencyFactory   = $currencyFactory;
        $this->storeManager      = $storeManager;
        $this->_sessionManagerInterface = $sessionManagerInterface;
        $this->_currencyData = $currencyData;
    }

    /**
     * Currency switcher before plugin
     *
     * @param SwitchActionParent $subject
     *
     * @return void
     */
    public function beforeExecute(SwitchActionParent $subject)
    {
        $items = $this->cart->getQuote()->getItemsCollection();
        $currencyObj = $this->currencyFactory->create();
        $baseCurrency = $this->_currencyData->getBaseCurrency();
        $newCurrency = (string) $subject->getRequest()->getParam('currency');
        $this->_sessionManagerInterface->start();

        $giftValue = $this->_checkoutSession->getGifts();

        if ($newCurrency != $baseCurrency && $giftValue) {
            /* Update Price for Other Currency */
            $rate = $currencyObj->load($baseCurrency)->getAnyRate($newCurrency);
            $giftValue *= $rate;
            $giftValue = (float) str_replace(',', '',($currencyObj
                                        ->format(
                                            $giftValue,
                                            ['display' => 1],
                                            false
                                        )));
            $this->_checkoutSession->setGift($giftValue);
        } elseif ($giftValue) {
            $this->_checkoutSession->setGift($giftValue);
        }
        
        foreach ($items as $item) {
            if ($item->getProductType() == "giftcertificate") {
                $session_array = $_SESSION['session_array']; // @codingStandardsIgnoreLine
                foreach ($session_array as $session_item) {
                    foreach ($session_item as $session_product) {
                        $item_sku = $item->getsku();
                        if ($item->getProductType() == "giftcertificate" && $item_sku == $session_product['sku']) {
                            $this->checkCurrency($newCurrency, $baseCurrency, $currencyObj, $session_product, $item);
                        }
                    }
                }
            }
        }
    }

    /**
     * Check Currency
     *
     * @param string $newCurrency
     * @param string $baseCurrency
     * @param object $currencyObj
     * @param array  $session_product
     * @param object $item
     */
    public function checkCurrency($newCurrency, $baseCurrency, $currencyObj, $session_product, $item)
    {
        if ($newCurrency != $baseCurrency) {
            /* Update Price for Other Currency */
            $rate = $currencyObj->load($baseCurrency)->getAnyRate($newCurrency);
            $session_product['price'] *= $rate;
            $session_product['price'] = $currencyObj
                                        ->format(
                                            $session_product['price'],
                                            ['display' => 1],
                                            false
                                        );
            $item->setCustomPrice($session_product['price']);
            $item->setOriginalCustomPrice($session_product['price']);
            $item->save();
        } else {
            /* Update Price for Base Currency */
            $item->setCustomPrice($session_product['price']);
            $item->setOriginalCustomPrice($session_product['price']);
            $item->getProduct()->setIsSuperMode(true);
            $item->save();
        }
    }
}
