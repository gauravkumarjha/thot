<?php
/*
 * NOTICE OF LICENSE
 *
 * This source file is subject to the NekloEULA that is bundled with this package in the file LICENSE.txt.
 *
 * It is also available through the world-wide-web at this URL: http://store.neklo.com/LICENSE.txt
 *
 * Copyright (c)  Neklo (http://store.neklo.com/)
 */

declare(strict_types=1);

namespace Neklo\ProductPosition\Plugin\ImportExport\Model;

use Magento\CatalogImportExport\Model\Import\Product;
use Magento\CatalogImportExport\Model\Import\Product\CategoryProcessor;
use Magento\Framework\Exception\LocalizedException;
use Magento\ImportExport\Model\Import;
use Neklo\ProductPosition\Model\ResourceModel\Product\Status;

class ImportPlugin
{
    public const CATALOG_PRODUCT = 'catalog_product';

    /**
     * @var Status
     */
    private $statusResource;

    /**
     * @var CategoryProcessor
     */
    private $categoryProcessor;

    /**
     * @param Status $statusResource
     * @param CategoryProcessor $categoryProcessor
     */
    public function __construct(
        Status $statusResource,
        CategoryProcessor $categoryProcessor
    ) {
        $this->statusResource = $statusResource;
        $this->categoryProcessor = $categoryProcessor;
    }

    /**
     * Import product positions
     *
     * @param Import $subject
     * @param bool $import
     * @return bool
     * @throws LocalizedException
     */
    public function afterImportSource(Import $subject, bool $import): bool
    {
        if ($subject->getEntity() == self::CATALOG_PRODUCT && $subject->getBehavior() == Import::BEHAVIOR_APPEND) {
            $source = $subject->getDataSourceModel();
            $separator = $subject->getData('_import_multiple_value_separator');
            $data = $source->getNextBunch();
            $categoryIds = [];
            $categories = [];

            foreach ($data as $iterator => $rowInfo) {
                $categoriesString = empty($rowInfo[Product::COL_CATEGORY]) ? '' : $rowInfo[Product::COL_CATEGORY];
                if (!empty($categoriesString)) {
                    $categories[] = $this->categoryProcessor->upsertCategories($categoriesString, $separator);
                }
            }

            if (!empty($categories)) {
                foreach ($categories as $iterator => $categoriesProduct) {
                    foreach ($categoriesProduct as $category) {
                        $categoryIds[] = $category;
                    }
                }

                $categoryIds = array_unique($categoryIds);
                foreach ($categoryIds as $categoryId) {
                    $this->statusResource->checkCategory((int) $categoryId);
                }
            }
        }

        return $import;
    }
}
