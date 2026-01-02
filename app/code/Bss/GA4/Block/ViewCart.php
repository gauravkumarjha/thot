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
namespace Bss\GA4\Block;

use Bss\GA4\Helper\Data;
use Bss\GA4\Model\Config;
use Bss\GA4\Model\DataItem;
use Magento\Catalog\Helper\Product\Configuration;
use Magento\Checkout\Block\Cart\Crosssell;
use Magento\Checkout\Model\Session;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class ViewCart extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Session
     */
    protected $checkoutSession;

    /**
     * @var Data
     */
    protected $dataHelper;

    /**
     * @var DataItem
     */
    protected $additionalData;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Configuration
     */
    protected $configuration;

    /**
     * @var Crosssell
     */
    protected $crosssell;

    /**
     * @param Context $context
     * @param Session $checkoutSession
     * @param Data $dataHelper
     * @param DataItem $additionalData
     * @param Config $config
     * @param Configuration $configuration
     * @param Crosssell $crosssell
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Bss\GA4\Helper\Data $dataHelper,
        \Bss\GA4\Model\DataItem $additionalData,
        \Bss\GA4\Model\Config $config,
        \Magento\Catalog\Helper\Product\Configuration $configuration,
        \Magento\Checkout\Block\Cart\Crosssell $crosssell,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $data
        );
        $this->checkoutSession = $checkoutSession;
        $this->dataHelper = $dataHelper;
        $this->additionalData = $additionalData;
        $this->config = $config;
        $this->configuration = $configuration;
        $this->crosssell = $crosssell;
    }

    /**
     * Get collection
     *
     * @return array|string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException|\Zend_Db_Statement_Exception
     */
    public function getCollection()
    {
        $productCollection = $this->checkoutSession->getQuote()->getItems();
        if ($productCollection) {
            $items = [];
            foreach ($productCollection as $key => $product) {
                $item = $this->additionalData->getDataItemCheckout($product, $key + 1);
                $item['coupon'] = $this->getCouponCode();
                $items[] = $item;
            }
            return $items;
        }
        return '';
    }

    /**
     * Get crossell product
     *
     * @return array|bool
     */
    public function getCrossellProduct()
    {
        $crossSellProducts = $this->crosssell->getItems();
        if ($crossSellProducts) {
            $index = 1;
            $items = [];
            foreach ($crossSellProducts as $upSellProduct) {
                $item = $this->additionalData->renderItem($upSellProduct, $index);
                $item['item_list_id'] = $this->checkoutSession->getQuoteId();
                $item['item_list_name'] = __("Cross-Sell Products");
                $items['items'][] = $item;
                $index++;
            }
            return $items['items'];
        }
        return false;
    }

    /**
     * Get coupon code
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCouponCode()
    {
        $quote = $this->checkoutSession->getQuote();
        if ($quote->getCouponCode()) {
            return $quote->getCouponCode();
        }
        return '';
    }

    /**
     * Get value
     *
     * @return mixed
     */
    public function getValue()
    {
        return (float)$this->checkoutSession->getQuote()->getGrandTotal();
    }

    /**
     * Get currency
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCurrency()
    {
        return $this->dataHelper->getCurrency();
    }

    /**
     * Serialize item
     *
     * @param array $item
     * @return bool|string
     */
    public function serializeItem($item)
    {
        return $this->dataHelper->serializeItem($item);
    }

    /**
     * Is enable module
     *
     * @return mixed
     */
    public function isEnableModule()
    {
        return $this->config->enableModule();
    }

    /**
     * Get quote id
     *
     * @return int|void
     */
    public function getQuoteId()
    {
        if ($this->checkoutSession->getQuoteId()) {
            return $this->checkoutSession->getQuoteId();
        }
    }

    /**
     * Escaper.
     *
     * @return \Magento\Framework\Escaper
     */
    public function escaper()
    {
        return $this->_escaper;
    }
}
