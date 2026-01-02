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
 * @copyright  Copyright (c) 2023-2023 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\GA4\Block\Order\MultiShipping;

use Bss\GA4\Helper\Data;
use Bss\GA4\Model\Config;
use Bss\GA4\Model\Config\Source\Attribute;
use Bss\GA4\Model\DataItem;
use Magento\Checkout\Model\Session;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Model\OrderRepository;

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
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @param Context $context
     * @param Data $dataHelper
     * @param Config $config
     * @param Attribute $attribute
     * @param Session $checkoutSession
     * @param DataItem $additionalData
     * @param OrderRepository $orderRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Bss\GA4\Helper\Data $dataHelper,
        \Bss\GA4\Model\Config $config,
        \Bss\GA4\Model\Config\Source\Attribute $attribute,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Bss\GA4\Model\DataItem $additionalData,
        \Magento\Sales\Model\OrderRepository $orderRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
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
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Get data event
     *
     * @return array|bool
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException|\Zend_Db_Statement_Exception
     */
    public function getMultiAddressItem()
    {
        if ($this->getOrder()) {
            $orderLists = $this->getOrder();
            $multiAddressItem = [];
            foreach ($orderLists as $order) {
                $items = [];
                $anAddressItem = [];
                $index = 1;
                foreach ($order->getItems() as $key => $item) {
                    if (!$item->getParentItemId() && $item->getProductType() != "configurable") {
                        $data = $this->additionalData->renderItem($item);
                        if ($this->getQty($item->getProductOptions())) {
                            if ($item->getProductType() == "grouped") {
                                $data['quantity'] = (float)$item->getQtyOrdered();
                            } else {
                                $data['quantity'] = (float)$this->getQty($item->getProductOptions());
                            }
                        }
                        $data['price'] = $item->getPrice();
                        $data['index'] = $index;
                        $items[] = $data;
                    }
                    if ($item->getParentItem() && $item->getParentItem()->getProductType() == "configurable") {
                        $data = $this->additionalData->renderItem($item, $key + 1);
                        $attributeInfo = $item->getParentItem()->getProductOptions()['attributes_info'];
                        $data["item_variant"] = $this->additionalData->getAttributeInfo($attributeInfo);
                        $data['quantity'] = (float)$this->getQty($item->getParentItem()->getProductOptions());
                        $data['price'] = $item->getParentItem()->getPrice();
                        $data['index'] = $index;
                        $items[] = $data;
                    }
                    $index++;
                }
                $anAddressItem['item'] = $items;
                $anAddressItem['value'] = $this->getValue($order);
                $anAddressItem['transactionId'] = $this->getTransactionId($order);
                $anAddressItem['shippingAmount'] = $this->getShippingAmount($order);
                $anAddressItem['taxAmount'] = $this->getTaxAmount($order);
                $anAddressItem['couponCode'] = $this->getCouponCode($order);
                $anAddressItem['isExclude'] = $this->isExclude($order);
                $multiAddressItem[] =  $anAddressItem;
            }
            return $multiAddressItem;
        }
        return false;
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
        $ids = $this->_session->getOrderIds();
        if ($ids && is_array($ids)) {
            $orderList = [];
            foreach ($ids as $id) {
                $searchCriteria = $this->searchCriteriaBuilder
                    ->addFilter('increment_id', $id, 'eq')->create();
                $orderList[] = array_first($this->orderRepository->getList($searchCriteria)->getItems());
            }
            return $orderList;
        }
        return false;
    }

    /**
     * Get value
     *
     * @return float
     */
    public function getValue($order)
    {
        if ($this->config->getConfigExcludeShippingTransaction()) {
            return (float)$order->getGrandTotal() - $this->getShippingAmount($order);
        }
        return (float)$order->getGrandTotal();
    }

    /**
     * Get transaction id
     *
     * @return mixed
     */
    public function getTransactionId($order)
    {
        return $order->getIncrementId();
    }

    /**
     * Get shipping amount
     *
     * @return float
     */
    public function getShippingAmount($order)
    {
        return (float)$order->getShippingAmount();
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
    public function getTaxAmount($order)
    {
        return (float)$order->getTaxAmount();
    }

    /**
     * Get coupon code
     *
     * @return mixed
     */
    public function getCouponCode($order)
    {
        return $order->getCouponCode();
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
    public function isExclude($order)
    {
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
