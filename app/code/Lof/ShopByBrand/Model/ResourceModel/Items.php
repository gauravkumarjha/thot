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

namespace Lof\ShopByBrand\Model\ResourceModel;

class Items extends \Ves\Brand\Model\ResourceModel\Brand
{
    /**
     * get brand products by brand id
     * @param int $brand_id
     * @return mixed
     */
    public function getBrandProducts($brand_id = 0)
    {
        $result = [];
        if ($brand_id) {
            $connection = $this->getConnection();
            $select = $connection->select()->from(
                $this->getTable('ves_brand_product'),
                '*'
            )
            ->where(
                'brand_id = ?',
                (int)$brand_id
            )
            ->group('product_id');
            $result = $connection->fetchAll($select);
        }
        return $result;
    }

    public function putBrandProducts($brand_id = 0, $products = [])
    {
        $table = $this->getTable('ves_brand_product');
        $where = ['brand_id = ?' => (int)$brand_id];
        $this->getConnection()->delete($table, $where);
        $data = [];
        $items = [];
        foreach ($products as $k => $_post) {
            if (!$_post->getProductId()) {
                continue;
            }
            $data[] = [
            'brand_id' => (int)$brand_id,
            'product_id' => $_post->getProductId(),
            'position' => (int)$_post->getPosition()
            ];

            //update product attributes
            try {
                $_is_update_attribute = true;
                $_product = $this->_productRepository->getById($_post->getProductId());
                $_product_brands = $_product->getData('product_brand');
                $_brands = [];
                if ($_product_brands) {
                    $_brands = !is_array($_product_brands)?explode(",", $_product_brands):$_product_brands;
                    if ($_brands && in_array($brand_id, $_brands)) {
                        $_is_update_attribute = false;
                    }
                }
                if ($_is_update_attribute) {
                    $_brands[] = $brand_id;
                    $attributes = ['product_brand' => implode(",", $_brands)];
                    if ($insert) {
                        foreach ($insert as $storeId) {
                            $this->_action->updateAttributes([$_post->getProductId()], $attributes, $storeId);
                        }
                    } else {
                        $this->_action->updateAttributes([$_post->getProductId()], $attributes, 0);
                    }
                }
            } catch (Exception $e) {
                throw new CouldNotSaveException(__(
                    'Could not save products for the brand: %1',
                    $exception->getMessage()
                ));
            }
        }

        $this->getConnection()->insertMultiple($table, $data);
        return true;
    }
}
