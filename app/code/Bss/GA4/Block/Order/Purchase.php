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
namespace Bss\GA4\Block\Order;

use Bss\GA4\Helper\Data;
use Bss\GA4\Model\Config;
use Bss\GA4\Model\Config\Source\Attribute;
use Bss\GA4\Model\DataItem;
use Magento\Checkout\Model\Session;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class Purchase extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Data
     */
    protected $dataHelper;

    /**
     * @var null
     */
    protected $order;

    /**
     * @var Attribute
     */
    protected $attribute;

    /**
     * @var Session
     */
    protected $checkoutSession;

    /**
     * @var DataItem
     */
    protected $additionalData;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @param Context $context
     * @param Data $dataHelper
     * @param Config $config
     * @param Attribute $attribute
     * @param Session $checkoutSession
     * @param DataItem $additionalData
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Bss\GA4\Helper\Data $dataHelper,
        \Bss\GA4\Model\Config $config,
        \Bss\GA4\Model\Config\Source\Attribute $attribute,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Bss\GA4\Model\DataItem $additionalData,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $data
        );
        $this->dataHelper = $dataHelper;
        $this->attribute = $attribute;
        $this->checkoutSession = $checkoutSession;
        $this->additionalData = $additionalData;
        $this->config = $config;
    }

    /**
     * Get item
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException|\Zend_Db_Statement_Exception
     */
    public function getItemsEvent()
    {
        $order = $this->getOrder();
        $items = [];
        $index = 1;
        foreach ($order->getItems() as $key => $item) {
            if (!$item->getParentItemId() && $item->getProductType() != "configurable") {
                $data = $this->additionalData->renderItem($item, $index);
                if ($this->getQty($item->getProductOptions())) {
                    if ($item->getProductType() == "grouped") {
                        $data['quantity'] = (float)$item->getQtyOrdered();
                    } else {
                        $data['quantity'] = (float)$this->getQty($item->getProductOptions());
                    }
                }
                $items[] = $data;
            }
            if ($item->getParentItem() && $item->getParentItem()->getProductType() == "configurable") {
                $data = $this->additionalData->renderItem($item, $key + 1);
                $attributeInfo = $item->getParentItem()->getProductOptions()['attributes_info'];
                $data["item_variant"] = $this->additionalData->getAttributeInfo($attributeInfo);
                $data['quantity'] = (float)$this->getQty($item->getParentItem()->getProductOptions());
                $items[] = $data;
            }
            $index++;
        }
        return $items;
    }

    /**
     * Get qty
     *
     * @param array $options
     * @return int
     */
    public function getQty($options)
    {
        if ($options && isset($options['info_buyRequest'])) {
            if (isset($options['info_buyRequest']['qty'])) {
                return $options['info_buyRequest']['qty'];
            }
        }
        return 1;
    }

    /**
     * Get order
     *
     * @return array|mixed|string|null
     */
    public function getOrder()
    {
        if ($this->order) {
            return $this->order;
        }
        $order = $this->getData('order');
        if ($order) {
            return $order;
        }
        return $this->checkoutSession->getLastRealOrder();
    }

    /**
     * Get value
     *
     * @return float
     */
    public function getValue()
    {
        $order = $this->getOrder();
        if ($this->config->getConfigExcludeShippingTransaction()) {
            return (float)$order->getGrandTotal() - $this->getShippingAmount();
        }
        return (float)$order->getGrandTotal();
    }

    /**
     * Get transaction id
     *
     * @return mixed
     */
    public function getTransactionId()
    {
        $order = $this->getOrder();
        return $order->getIncrementId();
    }

    /**
     * Get shipping amount
     *
     * @return float
     */
    public function getShippingAmount()
    {
        return (float)$this->getOrder()->getShippingAmount();
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
     * Get tax amount
     *
     * @return float
     */
    public function getTaxAmount()
    {
        return (float)$this->getOrder()->getTaxAmount();
    }

    /**
     * Get coupon code
     *
     * @return mixed
     */
    public function getCouponCode()
    {
        return $this->getOrder()->getCouponCode();
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
     * Check exclude zero order
     *
     * @return mixed
     */
    public function isExclude()
    {
        $order = $this->getOrder();
        if ($this->config->getConfigExcludeOrder() && $order->getGrandTotal() <= 0) {
            return true;
        }
        return false;
    }

    /**
     * Get affiliation
     *
     * @return string|null
     */
    public function getAffiliation()
    {
        $code = $this->config->getItemAffiliation();
        if ($code) {
            return $this->attribute->getAttributeLabelByCode($code);
        }
        return '';
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
