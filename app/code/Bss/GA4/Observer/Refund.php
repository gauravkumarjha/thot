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

namespace Bss\GA4\Observer;

use Bss\GA4\Model\Config;
use Bss\GA4\Model\DataItem;
use Magento\Customer\Model\Session;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Creditmemo\Item;

/**
 * @SuppressWarnings(PHPMD.CookieAndSessionMisuse)
 */
class Refund implements ObserverInterface
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var DataItem
     */
    protected $additionalData;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @param Session $customerSession
     * @param DataItem $additionalData
     * @param Config $config
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Bss\GA4\Model\DataItem $additionalData,
        \Bss\GA4\Model\Config $config
    ) {
        $this->customerSession = $customerSession;
        $this->additionalData = $additionalData;
        $this->config = $config;
    }

    /**
     * Create block and template event
     *
     * @param Observer $observer
     * @return void
     * @throws LocalizedException
     * @throws NoSuchEntityException|\Zend_Db_Statement_Exception
     */
    public function execute(Observer $observer)
    {
        if ($this->customerSession->getRefundData()) {
            $this->customerSession->unsRefundData();
        }
        $creditmemo = $observer->getCreditmemo();
        if (!$this->isExclude($creditmemo->getOrder())) {
            $data = [
                'items' => $this->getItems($creditmemo->getAllItems()),
                'value' => $this->getValue($creditmemo),
                'transactionId' => $creditmemo->getOrder()->getIncrementId(),
                'shippingAmount' => $creditmemo->getShippingAmount(),
                'taxAmount' => $creditmemo->getTaxAmount(),
                'couponCode' => $creditmemo->getOrder()->getCouponCode() ?? ''
            ];
            $this->customerSession->setRefundData($data);
        } else {
            $this->customerSession->setRefundData(false);
        }
    }

    /**
     * Get data additional
     *
     * @param Item[] $creditmemoItems
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException|\Zend_Db_Statement_Exception
     */
    public function getItems($creditmemoItems)
    {
        $prepareItems = [];
        foreach ($creditmemoItems as $key => $item) {
            if ($item->getOrderItem()->getProductType() == "configurable") {
                continue;
            }
            if ($item->getOrderItem()->getProductType() != "configurable") {
                $data = $this->additionalData->renderItem($item, $key + 1);
                $data['discount'] = $item->getDiscountAmount() ?? 0;
                $data['quantity'] = $item->getQty();
                $data['price'] = $item->getPrice();
            }
            if ($item->getOrderItem()->getProductType() != "configurable" && $item->getOrderItem()->getParentItem() &&
                $item->getOrderItem()->getParentItem()->getProductType() == "configurable") {
                if ($this->config->getItemId() == 'id') {
                    $data["item_id"] = $item->getProductId();
                } else {
                    $data["item_id"] = $item->getSku();
                }
                $data['price'] = $item->getOrderItem()->getParentItem()->getPrice();
                $data['item_variant'] = $this->getVariantConfigurable($item->getOrderItem()->getParentItem());
            }
            if (isset($data)) {
                $prepareItems[] = $data;
            }
        }
        return $prepareItems;
    }

    /**
     * Get variant
     *
     * @param Item|OrderItemInterface $parentItem
     * @return string
     */
    public function getVariantConfigurable($parentItem)
    {
        if ($parentItem->getProductOptions()) {
            $attributes = $parentItem->getProductOptions()['attributes_info'];
            $variant = [];
            foreach ($attributes as $attribute) {
                $variant[] = $attribute['label'] . ': ' . $attribute['value'];
            }
            return  implode(',', $variant);
        }
        return '';
    }

    /**
     * Get value
     *
     * @param Order $order
     * @return float
     */
    public function getValue($creditmemo)
    {
        if ($this->config->getConfigExcludeShippingTransaction()) {
            return (float)$creditmemo->getGrandTotal() - $creditmemo->getShippingAmount();
        }
        return (float)$creditmemo->getGrandTotal();
    }

    /**
     * Is exclude zero order
     *
     * @param Order $order
     * @return bool
     */
    public function isExclude($order)
    {
        if ($this->config->getConfigExcludeOrder() && $order->getGrandTotal() <= 0) {
            return true;
        }
        return false;
    }
}
