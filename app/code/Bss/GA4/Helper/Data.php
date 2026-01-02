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
 * @copyright  Copyright (c) 2022-2023 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\GA4\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Store\Model\StoreManagerInterface;

class Data extends AbstractHelper
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $pricingHelper;

    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    protected $productMetadata;

    /**
     * @var bool
     */
    protected $isMagentoEE;

    /**
     * Construct.
     *
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param SerializerInterface $serializer
     * @param \Magento\Framework\Pricing\Helper\Data $pricingHelper
     * @param \Magento\Framework\App\ProductMetadataInterface $productMetadata
     */
    public function __construct(
        Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        SerializerInterface $serializer,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata
    ) {
        parent::__construct($context);
        $this->storeManager = $storeManager;
        $this->serializer = $serializer;
        $this->pricingHelper = $pricingHelper;
        $this->productMetadata = $productMetadata;
    }

    /**
     * Get current currency
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCurrency()
    {
        return $this->storeManager->getStore()->getCurrentCurrency()->getCurrencyCode();
    }

    /**
     * Check magento Edition.
     *
     * @return bool
     */
    public function isMagentoEE()
    {
        if (empty($this->isMagentoEE)) {
            if ($this->productMetadata->getEdition() === "Enterprise") {
                $this->isMagentoEE = true;
            } else {
                $this->isMagentoEE = false;
            }
        }

        return $this->isMagentoEE;
    }

    /**
     * Convert prices between different currencies
     *
     * @param float $price
     * @return float
     */
    public function convertPriceCurrency($price)
    {
        return round($this->pricingHelper->currency($price, false, false), 3);
    }

    /**
     * Serialize item
     *
     * @param string|int|float|bool|array|null $item
     * @return bool|string
     */
    public function serializeItem($item)
    {
        return $this->serializer->serialize($item);
    }

    /**
     * UnSerialize item
     *
     * @param string $item
     * @return array|bool|float|int|string|null
     */
    public function unSerializeItem($item)
    {
        return $this->serializer->unserialize($item);
    }
}
