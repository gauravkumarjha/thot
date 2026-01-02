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

namespace Lof\ShopByBrand\Api;

interface BrandProductsManagementInterface
{

    /**
     * GET for brandProducts api
     * @param int $brand_id
     * @return \Lof\ShopByBrand\Api\Data\BrandProductInterface[]|string $products
     */
    public function getBrandProducts($brand_id);

    /**
     * GET for brandProducts api
     * @param int $brand_id
     * @param \Lof\ShopByBrand\Api\Data\BrandProductInterface[] $products
     * @return string
     */
    public function putBrandProducts($brand_id, $products);
}
