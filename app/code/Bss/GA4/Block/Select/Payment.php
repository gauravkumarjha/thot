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
namespace Bss\GA4\Block\Select;

use Bss\GA4\Helper\Data;
use Bss\GA4\Model\DataItem;
use Magento\Checkout\Model\Session;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Model\ResourceModel\Order\Payment\Collection;

class Payment extends \Magento\Framework\View\Element\Template
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
     * @var \Magento\Payment\Helper\Data
     */
    protected $paymentData;

    /**
     * @param Context $context
     * @param Session $checkoutSession
     * @param Data $dataHelper
     * @param DataItem $additionalData
     * @param \Magento\Payment\Helper\Data $paymentData
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Bss\GA4\Helper\Data $dataHelper,
        \Bss\GA4\Model\DataItem $additionalData,
        \Magento\Payment\Helper\Data $paymentData,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $data
        );
        $this->checkoutSession = $checkoutSession;
        $this->dataHelper = $dataHelper;
        $this->additionalData = $additionalData;
        $this->paymentData = $paymentData;
    }

    /**
     * Get list item event
     *
     * @return array
     */
    public function getListItems()
    {
        return $this->additionalData->getItemsPaymentShipping();
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
        return $this->checkoutSession->getQuote()->getGrandTotal();
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
     * Get payment Title
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getPaymentType()
    {
        if ($this->getRequest()->getParam('method')) {
            $paymentList = $this->paymentData->getPaymentMethodList();
            return $paymentList[$this->getRequest()->getParam('method')];
        }
        return $this->checkoutSession->getQuote()->getPayment()->getMethodInstance()->getTitle();
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
