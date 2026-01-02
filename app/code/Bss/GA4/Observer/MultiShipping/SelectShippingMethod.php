<?php
declare(strict_types=1);
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at thisURL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_GA4
 * @author     Extension Team
 * @copyright  Copyright (c) 2022-2022 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */

namespace Bss\GA4\Observer\MultiShipping;

use Bss\GA4\Helper\Data;
use Bss\GA4\Model\Config;
use Bss\GA4\Model\DataItem;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class SelectShippingMethod implements ObserverInterface
{
    /**
     * @var DataItem
     */
    protected $additionalData;

    /**
     * @var Data
     */
    protected $dataHelper;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Shipping\Model\Config
     */
    protected $shipconfig;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param DataItem $additionalData
     * @param Data $dataHelper
     * @param Config $config
     * @param Session $checkoutSession
     * @param \Magento\Shipping\Model\Config $shipConfig
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Bss\GA4\Model\DataItem $additionalData,
        \Bss\GA4\Helper\Data $dataHelper,
        \Bss\GA4\Model\Config $config,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Shipping\Model\Config $shipConfig,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->additionalData = $additionalData;
        $this->dataHelper = $dataHelper;
        $this->config = $config;
        $this->checkoutSession = $checkoutSession;
        $this->shipconfig = $shipConfig;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Set data multi shipping address
     *
     * @param Observer $observer
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Zend_Db_Statement_Exception
     */
    public function execute(Observer $observer)
    {
        $shippingMethod = $observer->getRequest()->getPost('shipping_method');
        $shippingMethods = $this->getShippingMethods();
        $quote = $observer->getQuote();
        $addresses = $quote->getAllShippingAddresses();
        $dataAddress = [];
        foreach ($addresses as $address) {
            $items = [];
            $index = 1;
            foreach ($address->getAllItems() as $product) {
                if ($product->getProduct()->getTypeId() == "configurable") {
                    continue;
                }
                $item = $this->additionalData->renderItem($product, $index);
                $item['discount'] = $product->getDiscountAmount() ?? 0;
                $item['quantity'] = $product->getQty();
                $item['price'] = $this->dataHelper->convertPriceCurrency($product->getPrice());
                if ($product->getParentItem() &&
                    $product->getParentItem()->getProduct()->getTypeId() == "configurable") {
                    if ($this->config->getItemId() == 'id') {
                        $item["item_id"] = $product->getProductId();
                    } else {
                        $item["item_id"] = $product->getSku();
                    }
                    $item['price'] = $this->dataHelper->convertPriceCurrency($product->getParentItem()->getQuoteItem()->getPrice());
                    $item['item_variant'] = $this->additionalData->getVariantConfigurable($product->getParentItem()->getProduct());
                }
                $index++;
                $items[] = $item;
            }
            $data['item'] = $items;
            $data['value'] = $address->getGrandTotal();
            $addressId = $address->getId();
            if (isset($shippingMethod[$addressId]) && isset($shippingMethods[$shippingMethod[$addressId]])) {
                $data['shipping_type'] = $shippingMethods[$shippingMethod[$addressId]];
            }
            $dataAddress[] = $data;
        }
        if ($dataAddress) {
            $this->checkoutSession->setMultiShippingData($dataAddress);
        } else {
            $this->checkoutSession->setMultiShippingData(false);
        }
    }

    /**
     * Get Shipping Methods
     *
     * @return array
     */
    public function getShippingMethods()
    {
        $methods = [];
        $activeCarriers = $this->shipconfig->getActiveCarriers();
        foreach ($activeCarriers as $carrierCode => $carrierModel) {
            if ($carrierMethods = $carrierModel->getAllowedMethods()) {
                foreach ($carrierMethods as $methodCode => $method) {
                    $code = $carrierCode . '_' . $methodCode;
                    $carrierTitle = $this->scopeConfig
                        ->getValue('carriers/' . $carrierCode . '/title');
                    $methods[$code] = $carrierTitle;
                }
            }
        }
        return $methods;
    }
}
