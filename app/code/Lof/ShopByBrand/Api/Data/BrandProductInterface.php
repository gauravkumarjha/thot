<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/terms
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lof_ShopByBrand
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */
declare(strict_types=1);

namespace Lof\ShopByBrand\Api\Data;

interface BrandProductInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

    const BRAND_ID = 'brand_id';
    const PRODUCT_ID = 'product_id';
    const POSITION = 'position';

    /**
     * Get brandId
     * @return string|null
     */
    public function getBrandId();

    /**
     * Set brandId
     * @param string $brandId
     * @return \Lof\ShopByBrand\Api\Data\BrandProductInterface
     */
    public function setBrandId($brandId);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Lof\ShopByBrand\Api\Data\BrandProductExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Lof\ShopByBrand\Api\Data\BrandProductExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Lof\ShopByBrand\Api\Data\BrandProductExtensionInterface $extensionAttributes
    );

    /**
     * Get product_id
     * @return int
     */
    public function getProductId();

    /**
     * Set product_id
     * @param int $product_id
     * @return \Lof\ShopByBrand\Api\Data\BrandProductInterface
     */
    public function setProductId($product_id);

    /**
     * Get name
     * @return int|null
     */
    public function getPosition();

    /**
     * Set name
     * @param int $position
     * @return \Lof\ShopByBrand\Api\Data\GroupInterface
     */
    public function setPosition($position);
}
