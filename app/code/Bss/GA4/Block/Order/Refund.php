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
use Magento\Customer\Model\Session;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class Refund extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Data
     */
    protected $dataHelper;

    /**
     * @var null
     */
    protected $refundData;

    /**
     * @var Attribute
     */
    protected $attribute;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @param Context $context
     * @param Data $dataHelper
     * @param Attribute $attribute
     * @param Session $customerSession
     * @param Config $config
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Bss\GA4\Helper\Data $dataHelper,
        \Bss\GA4\Model\Config\Source\Attribute $attribute,
        Session $customerSession,
        \Bss\GA4\Model\Config $config,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $data
        );
        $this->dataHelper = $dataHelper;
        $this->attribute = $attribute;
        $this->customerSession = $customerSession;
        $this->config = $config;
    }

    /**
     * Get order
     *
     * @return array|mixed|string|null
     */
    public function getDataRefund()
    {
        $dataRefund = $this->customerSession->getRefundData();
        if (!$this->refundData) {
            $this->refundData = $dataRefund;
        }
        return $this->refundData;
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
     *  Get affiliation
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
     * Is enable
     *
     * @return mixed
     */
    public function isEnable()
    {
        return $this->config->enableModule();
    }

    /**
     * Get measurement id
     *
     * @return mixed
     */
    public function getMeasurementId()
    {
        return $this->config->getMeasurementId();
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
