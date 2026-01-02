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
namespace Bss\GA4\Block\MultipleAddress\Select;

use Bss\GA4\Helper\Data;
use Bss\GA4\Model\DataItem;
use Magento\Checkout\Model\Session;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class Shipping extends \Magento\Framework\View\Element\Template
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
     * @param Context $context
     * @param Session $checkoutSession
     * @param Data $dataHelper
     * @param DataItem $additionalData
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Bss\GA4\Helper\Data $dataHelper,
        \Bss\GA4\Model\DataItem $additionalData,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $data
        );
        $this->checkoutSession = $checkoutSession;
        $this->dataHelper = $dataHelper;
        $this->additionalData = $additionalData;
    }

    /**
     * Get data multi address
     *
     * @return array|false
     */
    public function getListItems()
    {
        if ($this->checkoutSession->getMultiShippingData()) {
            return $this->checkoutSession->getMultiShippingData();
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
        if ($this->checkoutSession->getQuote()->getCouponCode()) {
            return $this->checkoutSession->getQuote()->getCouponCode();
        }
        return '';
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
     * Escaper.
     *
     * @return \Magento\Framework\Escaper
     */
    public function escaper()
    {
        return $this->_escaper;
    }
}
